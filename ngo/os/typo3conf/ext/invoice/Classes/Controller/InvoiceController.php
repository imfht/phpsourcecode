<?php
namespace Jykj\Invoice\Controller;


/***
 *
 * This file is part of the "发票管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * InvoiceController
 */
class InvoiceController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * invoiceRepository
     * 
     * @var \Jykj\Invoice\Domain\Repository\InvoiceRepository
     * @inject
     */
    protected $invoiceRepository = null;
    
    /**
     * channelsRepository
     *
     * @var \Jykj\Invoice\Domain\Repository\ChannelsRepository
     * @inject
     */
    protected $channelsRepository = NULL;
    
    public function initializeAction(){
        if($this->request->hasArgument('invoice')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('invoice')->getPropertyMappingConfiguration();
            //时间类型修改
            $propertyMappingConfiguration->forProperty('donatetime')->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d' );
        }
    }

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        
        if($_GET["tx_invoice_fpgl"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_invoice_fpgl"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        $this->view->assign('page', $page);
        
        $keyword=$this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $invoices = $this->invoiceRepository->findAlls($keyword);
        $this->view->assign('invoices', $invoices);
        $this->view->assign('keyword', $keyword);
        
        //excel export
        if($this->request->hasArgument('excelExport')){
            $phpExcelService = GeneralUtility::makeInstanceService('phpexcel');
            $phpExcel = $phpExcelService->getPHPExcel();
            $sheet  = $phpExcel->setActiveSheetIndex(0);
            $dataArray[] = array('收据抬头', '捐赠时间', '税号', '捐赠金额', '捐赠渠道', '联系人',  '联系电话');
            if($invoices->count()){
                foreach($invoices as $invoice){
                    $dataArray[] = array(
                        $invoice->getHeader(),
                        $this->getDatetime($invoice->getdonatetime()),
                        $invoice->getSpare1(),
                        $invoice->getMoney(),
                        $invoice->getChannelid()->getName(),
                        $invoice->getPeople(),
                        $invoice->getTelphone(),
                    );
                }
                $sheet->fromArray($dataArray, NULL, 'A1');
                $objWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
                $fileName = '票据信息_'.date('Y-m-d');
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
        }
    }

    private function getDatetime($dateobj){
        $kval="";
        foreach ($dateobj as $key => $value){
            if($key=="date")  $kval = $value;
        }
        return substr($kval,0,10);
    }
    
    /**
     * action show
     * 
     * @param \Jykj\Invoice\Domain\Model\Invoice $invoice
     * @return void
     */
    public function showAction(\Jykj\Invoice\Domain\Model\Invoice $invoice)
    {
        $this->view->assign('invoice', $invoice);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $channels = $this->channelsRepository->findAll();
        $this->view->assign('channels', $channels);
    }

    /**
     * action create
     * 
     * @param \Jykj\Invoice\Domain\Model\Invoice $invoice
     * @return void
     */
    public function createAction(\Jykj\Invoice\Domain\Model\Invoice $invoice)
    {
        //var_dump($invoice);
        //$this->addFlashMessage('保存成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->invoiceRepository->add($invoice);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('success');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Invoice\Domain\Model\Invoice $invoice
     * @ignorevalidation $invoice
     * @return void
     */
    public function editAction(\Jykj\Invoice\Domain\Model\Invoice $invoice)
    {
        
        $channels = $this->channelsRepository->findAll();
        $this->view->assign('channels', $channels);
        $this->view->assign('invoice', $invoice);
    }

    /**
     * action update
     * 
     * @param \Jykj\Invoice\Domain\Model\Invoice $invoice
     * @return void
     */
    public function updateAction(\Jykj\Invoice\Domain\Model\Invoice $invoice)
    {
        $this->addFlashMessage('修改成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->invoiceRepository->update($invoice);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Invoice\Domain\Model\Invoice $invoice
     * @return void
     */
    public function deleteAction(\Jykj\Invoice\Domain\Model\Invoice $invoice)
    {
        $this->addFlashMessage('删除成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->invoiceRepository->remove($invoice);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * 成功跳转界面
     */
    public function successAction(){
        
    }
}
