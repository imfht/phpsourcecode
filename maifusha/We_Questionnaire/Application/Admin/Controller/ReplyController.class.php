<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 处理回答记录相关请求
 */
class ReplyController extends CommonController
{
    /**
     * 成绩表
     */
    public function all()
    {
        $this->bcItemPush('全部回答', U('Reply/all'));

        $replyRecords = M('Reply')->alias('r')
                                  ->join('left join questionnaires q on r.questionnaire_id = q.id')
                                  ->field('r.*,q.name q_name,q.type q_type,q.id q_id')
                                  ->select();

        $this->assign('replyRecords', $replyRecords);

        $this->display();
    }

    /**
     * 问卷基本分析
     */
    public function analyze(){
        $this->bcItemPush('问卷分析', U('Reply/analyze'));

        $questionnaires = M('Questionnaires')->getField('id,type,name');
        foreach ($questionnaires as $id => $questionnaire) {
            if( $questionnaire['type'] == 'exam' ){
                $scores = M('Reply')->where("questionnaire_id=$id")->getField('total_score', true);

                if( count($scores) ){
                    sort($scores);
                }else{
                    continue;
                }
                $scoreNum = count($scores);
                $max_score = $scores[$scoreNum - 1];
                $min_score = $scores[0];
                $max_nicknames = implode(',', M('Reply')->where("questionnaire_id=$id and total_score=$max_score")->getField('nickname', true) );
                $min_nicknames = implode(',', M('Reply')->where("questionnaire_id=$id and total_score=$min_score")->getField('nickname', true) );

                $static = array(
                    'average'       =>  round(array_sum($scores)/$scoreNum, 1),
                    'max'           =>  "$max_nicknames: $max_score",
                    'min'           =>  "$min_nicknames: $min_score"
                );
            }else{
                $static = array(
                    'average'       =>  '',
                    'max'           =>  '',
                    'min'           =>  ''
                );
            }

            $questionnaires[$id] = array_merge($questionnaire, $static);
            $questionnaires[$id]['member_num'] = M('Reply')->where("questionnaire_id=$id")->count();
        }

        $this->assign('questionnaires', $questionnaires);

        $this->display();
    }

    /**
     * 问卷详情分析
     */
    public function statistic()
    {
        $this->bcItemPush('问卷分析', U('Reply/analyze'));
        $this->bcItemPush('详情分析');

        /* 问卷概览信息 */
        $qnID = I('get.questionnaireID/d');
        $questionnaire = M('Questionnaires')->field('id,type,name,description')->find($qnID);
        $type = $questionnaire['type'];

        if( $type == 'exam' ){
            $scores = M('Reply')->where("questionnaire_id=$qnID")->getField('total_score', true);

            sort($scores);
            $scoreNum = count($scores);
            $questionnaire['average'] = round(array_sum($scores)/$scoreNum, 1);
            $questionnaire['max_score'] = $scores[$scoreNum - 1];
            $questionnaire['min_score'] = $scores[0];
            $questionnaire['max_nicknames'] = implode(',', M('Reply')->where("questionnaire_id=$qnID and total_score=$questionnaire[max_score]")->getField('nickname', true) );
            $questionnaire['min_nicknames'] = implode(',', M('Reply')->where("questionnaire_id=$qnID and total_score=$questionnaire[min_score]")->getField('nickname', true) );
        }

        /* 读出指定问卷的所有问题 */
        $questions = M('Questions')->alias('qt')
                                   ->join('left join questionnaires qn on qt.questionnaire_id=qn.id')
                                   ->where("qt.questionnaire_id=$qnID")
                                   ->getField('qt.id,qn.type questionnaire_type,qt.name,qt.options,qt.standard');

        /* 解析每个问题的选项 */
        $mapper = function ($item) {
            $item['options'] = json_decode($item['options'], true);

            if( $item['questionnaire_type'] == 'exam' )
            {
                $item['rate'] = 0;
                $item['rightCnt'] = 0;
                $item['falseCnt'] = 0;
            }

            return $item;
        };
        $questions = array_map($mapper, $questions);

        /* 开始计算每道题的准确率和统计每道题的每个选项的勾选情况 */
        $replys = M('Reply')->where("questionnaire_id=$qnID")->getField('reply', true);

        foreach ($replys as $reply) {
            $reply = json_decode($reply, true); //一套卷子的回答

            foreach ($reply as $questionID => $answer) { //$answer -- 卷子内一个问题的回答
                /* 针对考试卷累计正误 */
                if( $type == 'exam' ){
                    if( $answer == $questions[$questionID]['standard'] ){
                        $questions[$questionID]['rightCnt']++;
                    }else{
                        $questions[$questionID]['falseCnt']++;
                    }
                }

                if( preg_match('/^%u/', $answer) ){ //case: 简答题,  收集回答的前10个宽字符
                    $answer = unicodeDecode($answer);
                    $tag = mb_substr($answer, 0, 10, 'UTF-8');
                    $tag .= (mb_strlen($answer, 'UTF-8') > 10)? "...;\n\n" : ";\n\n";
                    $questions[$questionID]['options'][0]['replyList'][] = "\n".$tag; //收集简答题回答的一个摘要, 简答题只有一个选项且键为0
                }else{ //case: 勾选题, 统计勾选情况
                    $chooseList = explode(',', $answer);
                    foreach ($chooseList as $choose) {
                        $chooseInfo = explode(':', $choose);
                        $optionIndex = $chooseInfo[0];
                        $optionOthertext = isset($chooseInfo[1]) ? $chooseInfo[1] : null;

                        if( !isset($questions[$questionID]['options'][$optionIndex]['count']) ){
                            $questions[$questionID]['options'][$optionIndex]['count'] = 0;
                        }
                        $questions[$questionID]['options'][$optionIndex]['count']++; //累计勾选次数
                        if( isset($optionOthertext) ){ //case: 勾选题型里面的其它问题,  收集回答的前10个宽字符
                            $answer = unicodeDecode($optionOthertext);
                            $tag = mb_substr($answer, 0, 10, 'UTF-8');
                            $tag .= (mb_strlen($answer, 'UTF-8') > 10)? "...;\n\n" : ";\n\n";
                            $questions[$questionID]['options'][$optionIndex]['replyList'][] = "\n".$tag; //收集回答摘要
                        }
                    }
                }

            }

        }

        /* 针对考试卷, 计算每道题的准确率 */
        if( $type == 'exam' ){
            foreach ($questions as $index => $question) {
                $rightCnt = $questions[$index]['rightCnt'];
                $falseCnt = $questions[$index]['falseCnt'];
                $questions[$index]['rate'] = round(($rightCnt/($rightCnt+$falseCnt)), 3)*100;

                $standMap = function($item){
                    $tmp = explode(':', $item);

                    if( !preg_match('/^%u/', $tmp[0]) ){ //勾选题的索引号增加1
                        $tmp[0]++;
                    }

                    return unicodeDecode( implode(': ', $tmp) );
                };
                $questions[$index]['standard'] = array_map($standMap, explode(',', $questions[$index]['standard']));
            }
        }

        $this->assign('questionnaire', $questionnaire);
        $this->assign('questions', $questions);

        $this->display();
    }

    /**
     * 删除指定回答记录
     */
    public function delete()
    {
        if( is_array(I('id')) ){ //批量删除
            $id = implode( ',', I('id', array(), 'intval') );
        }else{ //单个删除
            $id = I('id/d');
        }

        $state = M('Reply')->delete($id);

        if( $state === false ){
            $this->error('回答记录删除失败，错误信息: '.M('Reply')->getDbError());
        }else{
            $this->success('回答记录删除成功', '/Reply/all');
        }
    }
}