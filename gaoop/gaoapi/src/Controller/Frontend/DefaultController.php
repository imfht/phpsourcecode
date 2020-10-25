<?php


namespace App\Controller\Frontend;


use App\Entity\Info;
use App\Library\Helper\GeneralHelper;
use App\Message\BuildDocumentRedisData;
use App\Service\Redis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * User: gao
     * Date: 2019/11/5
     * Description:
     * @Route("/", name="home_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $pagination = $this->getDoctrine()->getRepository(Info::class)->getInfoList($page, $limit);

        return $this->render('Frontend/default/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: ~
     * @param $tag
     * @Route("/swagger/{tag}", name="swagger_tag_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function swagger($tag)
    {
        $info = $this->getDoctrine()->getRepository(Info::class)->findOneBy(['tag' => $tag]);
        if (!is_object($info)) {
            throw $this->createNotFoundException('未找到对应的API文档');
        }

        $version = time();
        return $this->render('Frontend/default/swagger.html.twig', [
            'info' => $info,
            'version' => $version,
        ]);
    }

    /**
     * User: Gao
     * Date: 2020/2/22
     * Description: 获取接口内容
     * @param Request $request
     * @param Redis $redis
     * @Route("/document/content", name="document_content")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getApiDocContent(Request $request, Redis $redis)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $result = [
                'success' => false,
                'message' => '',
                'data' => []
            ];

            $info_id = $request->get('info_id', 0);
            $operation_id = $request->get('operation_id', 0);
            $redis_prefix = GeneralHelper::getOneInstance()->getDocumentRedisPrefixKey($info_id, Info::REDIS_DOCUMENT_PATH_PREFIX_KEY);
            $redis_key = $redis_prefix . ':' . $operation_id;
            if ($redis->exists($redis_key)) {
                $result['success'] = true;
                $result['data'] = json_decode($redis->get($redis_key), true);
            } else {
                // 尝试重新生成文档缓存数据
                $this->dispatchMessage(new BuildDocumentRedisData($info_id, false));
            }

            return $this->json($result);
        }
    }

    /**
     * User: gao
     * Date: 2019/11/29
     * Description: ~
     * @param $tag
     * @param $redis
     * @Route("/document/{tag}", name="document_tag_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function document($tag, Redis $redis)
    {
        $info = $this->getDoctrine()->getRepository(Info::class)->findOneBy(['tag' => $tag]);
        if (!is_object($info)) {
            throw $this->createNotFoundException('未找到对应的API文档');
        }

        $tags = [];
        $redis_menu_key = GeneralHelper::getOneInstance()->getDocumentRedisPrefixKey($info->getId(), Info::REDIS_DOCUMENT_MENU_PREFIX_KEY);
        $redis_data = $redis->get($redis_menu_key);
        if (!is_null($redis_data)) {
            $tags = json_decode($redis_data, true);
        } else {
            // 尝试重新生成文档缓存数据
            $this->dispatchMessage(new BuildDocumentRedisData($info->getId(), false));
        }

        return $this->render('Frontend/default/document.html.twig', [
            'info' => $info,
            'tags' => $tags,
        ]);
    }

}