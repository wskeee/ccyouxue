<?php

namespace frontend\modules\game\controllers;

use common\models\games\GameHistory;
use common\models\games\GameLoginHistory;
use common\models\games\GameLoginQrcode;
use common\models\games\Scene;
use common\models\User;
use EasyWeChat\Foundation\Application;
use maxwen\easywechat\Wechat;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\Controller;
use function GuzzleHttp\json_encode;

/**
 * Default controller for the `game` module
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save-grade' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        /* @var $gameHistory GameHistory */
        $gameHistory;
        $val = floatval(349.120);

        $query = (new Query())
                ->select(['id','data'])
                ->from(GameHistory::tableName())
                ->where('data = :val', [':val' => $val]);
        
        $connection  = Yii::$app->db;
        $sql     = "SELECT `id`, `data` FROM `ccyx_game_history` WHERE `data`=349.12";
        $command = $connection->createCommand($sql);
        $res     = $command->queryAll();
        var_dump($res);
        var_dump($query->all(\Yii::$app->db));
        exit;
       
        //var_dump($query->createCommand()->getRawSql());
        var_dump($query->all(\Yii::$app->db));
        
        //return $this->render('index');
    }
    
    
    /**
     * 获取所有场景列表
     * 客户端获取到场景数据后，可能设置场景，后面通信都以设置场景有关
     * 这里不作权限验证
     */
    public function actionGetSceneList(){
        Yii::$app->getResponse()->format = 'json';
        $scenes = Scene::find()
                ->select(['id','name','des','qrcodeurl'])
                ->asArray()
                ->all();
        $data = [
            'scene_list'=>$scenes,
            'server'=>[
                'time'=>  time()*1000
            ]
        ];
        return [
            'code'=> 0,
            'data'=> $data,
            'msg'=> '',
        ];
    }
    
    /**
     * 获取新一轮游戏开始二维码
     * @param int $scene_id 场景id
     * @param int $game_id 赛事id
     */
    public function actionGetLoginQrcode($scene_id,$game_id=1){
        Yii::$app->getResponse()->format = 'json';
        
        $validateResult = $this->validateSign();
        if(!$validateResult['pass']){
            return [
                'code'=> 1,
                'msg'=> $validateResult['msg'],
            ];
        }
        
        $code = 1;
        $data = [];
        $msg = '获取开始游戏二维码失败！';
        /* @var $wechat Wechat */
        $wechat = Yii::$app->get('wechat');
        /* @var $app Application */
        $app = $wechat->app;
        $gameQrcode = GameLoginQrcode::findOne(['scene_id'=>$scene_id,'game_id'=>$game_id]);
        if($gameQrcode == null)
        {
            //id为10000以上
            $count = GameLoginQrcode::find()->count()+1;
            $gameQrcode = new GameLoginQrcode(['id'=>10000 + $count]);
        }
        
        $result = $app->qrcode->temporary($gameQrcode->id,10 * 60); //获取临时二维码票据
        $ticket = $result->ticket;                                  //获取二维码路径
        $expireSeconds = $result->expire_seconds;                   //有效秒数
        $gameQrcode->scene_id = $scene_id;
        $gameQrcode->game_id = $game_id;
        $gameQrcode->qrcodeurl = $app->qrcode->url($ticket);
        $gameQrcode->expire_time = $expireSeconds;
        if($gameQrcode->save()){
            $code = 0;
            $msg = '';
            $data = [
                'qrcode'=>[
                    'scene_id'=>$scene_id,
                    'game_id'=>$game_id,
                    'qrcodeurl'=>$gameQrcode->qrcodeurl,
                    'expire_time'=>$gameQrcode->expire_time,
                ]
            ];
        }
        return [
            'code'=> $code,
            'data'=> $data,
            'msg'=> $msg,
        ];
    }
    
    /**
     * 获取最新登录的用
     * @param int $scene_id 场景id
     * @param int $game_id 赛事id
     */
    public function actionGetReadyUser($scene_id,$game_id=1){
        Yii::$app->getResponse()->format = 'json';
        
        $validateResult = $this->validateSign();
        if(!$validateResult['pass']){
            return [
                'code'=> 1,
                'msg'=> $validateResult['msg'],
            ];
        }
        
        $code = 1;
        $msg = '';
        $data = [];
        $gameLoginHistory = GameLoginHistory::find()
                ->where(['scene_id' => $scene_id, 'game_id' => $game_id])
                ->andWhere('UNIX_TIMESTAMP(NOW())-created_at < 60')
                ->orderBy('created_at DESC')
                ->one();
        if($gameLoginHistory){
            /* @var $user User */
            $user = User::findOne(['id'=>$gameLoginHistory->u_id]);
            $code = 0;
            $bestHistory = $this->getUserBestRank($user->id, $game_id);                  //历史最好记录
            $monthBestHistory = $this->getUserBestRank($user->id, $game_id,  strtotime('first day of this month'), strtotime('last day of this month'));         //本月最好记录
            $data = [
                'user'=> $user->toArray(['id','nickname','sex','avatar']),
                'game'=>[
                    'is_first_login' => $this->getIsFirstLogin($user->id, $game_id),        //是否第一次登录 1是,0否
                    'last_login' => $this->getUserLastLoginTime($user->id, $game_id),       //最后一次登录
                    'play_num' => $this->getUserPlayNum($user->id, $game_id),               //游戏的次数
                    'best_grade' => $bestHistory['data'] ? $bestHistory['data'] : 0 ,                             //最好成绩
                    'best_rank' => $bestHistory['rank'] ? $bestHistory['rank'] : 0,                               //最高排位
                    'best_month_grade' => $monthBestHistory['data'] ? $monthBestHistory['data'] : 0,              //本月最好成绩
                    'best_month_rank' => $monthBestHistory['rank'] ? $monthBestHistory['rank'] : 0,               //当前月最高排位
                ]
            ];
        }else
            $msg = '用户未准备！';
        return [
            'code'=> $code,
            'data'=> $data,
            'msg'=> $msg,
        ];
    }
    
    /**
     * 获取排行榜
     * @param type $game_id         赛事id
     * @param type $rank_num        排行前几名
     * @param type $start_time      开始时间 
     * @param type $end_time        结束时间
     */
    public function actionGetRank($game_id=1,$rank_num=10,$start_time=null,$end_time=null){
        Yii::$app->getResponse()->format = 'json';
        
        $validateResult = $this->validateSign();
        if(!$validateResult['pass']){
            return [
                'code'=> 1,
                'msg'=> $validateResult['msg'],
            ];
        }
        
        $code = 1;
        $msg = '';
        $data = [
            'game_id' => $game_id,
            'rank_num' => $rank_num,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'rank_list' => $this->getGameRank($game_id, $rank_num, $start_time, $end_time),
        ];
        
        return [
            'code'=> $code,
            'data'=> $data,
            'msg'=> $msg,
        ];
    }
    
    /**
     * 获取当月排行榜
     * @param type $game_id
     * @param type $rank_num
     */
    public function actionGetMonthRank($game_id=1,$rank_num=10){
        Yii::$app->getResponse()->format = 'json';
        
        $validateResult = $this->validateSign();
        if(!$validateResult['pass']){
            return [
                'code'=> 1,
                'msg'=> $validateResult['msg'],
            ];
        }
        
        $code = 1;
        $msg = '';
        $data = [
            'game_id' => $game_id,
            'rank_num' => $rank_num,
            'rank_list' => $this->getGameRank($game_id, $rank_num, strtotime('first day of this month'), strtotime('last day of this month'))   
        ];
        
        return [
            'code'=> $code,
            'data'=> $data,
            'msg'=> $msg,
        ];
    }
    
    /**
     * 保存成绩
     */
    public function actionSaveGrade(){
        
        Yii::$app->getResponse()->format = 'json';
        
        $validateResult = $this->validateSign();
        if(!$validateResult['pass']){
            return [
                'code'=> 1,
                'msg'=> $validateResult['msg'],
            ];
        }
        
        $post = Yii::$app->getRequest()->getQueryParams();
        $post = array_merge($post,Yii::$app->getRequest()->getBodyParams());
        
        $game_id = isset($post['game_id']) ? $post['game_id'] : 1;
        $scene_id = isset($post['scene_id']) ? $post['scene_id'] : null;
        $grade = isset($post['grade']) ? $post['grade'] : null;
        $u_id = isset($post['u_id']) ? $post['u_id'] : null;
        
        $code = 1;
        $msg = '';
        $data = [];
        $monthStart = strtotime('first day of this month');
        $monthEnd = strtotime('last day of this month');
        
        if($scene_id != null && $grade != null && $u_id!=null){
            $curGameHistory = new GameHistory();
            $curGameHistory->u_id = $u_id;
            $curGameHistory->scene_id = $scene_id;
            $curGameHistory->game_id = $game_id;
            $curGameHistory->data = $grade;
            if($curGameHistory->save()){
                $code = 0;
                /* @var $user User */
                $user = User::findOne(['id'=>$u_id]);
                $bestHistory = $this->getUserBestRank($u_id, $game_id);                                        //历史最好记录
                $monthBestHistory = $this->getUserBestRank($u_id, $game_id,  $monthStart, $monthEnd);          //本月最好记录
                
                $data = [
                    'u_id' => $u_id,
                    'scene_id' => $scene_id,    //提交场景
                    'game_id' => $game_id,      //赛事id
                    'game_rank' =>[
                        'rank_num'=>10,
                        'rank_list'=>$this->getGameRank($game_id, 10, $monthStart, $monthEnd),  
                    ],
                    'best_grade' => $bestHistory['data'] ? $bestHistory['data'] : 0 ,                             //最好成绩
                    'best_rank' => $bestHistory['rank'] ? $bestHistory['rank'] : 0,                               //最高排位
                    'best_month_grade' => $monthBestHistory['data'] ? $monthBestHistory['data'] : 0,              //本月最好成绩
                    'best_month_rank' => $monthBestHistory['rank'] ? $monthBestHistory['rank'] : 0,               //当前月最高排位
                    'cur_grade' => $grade,                                                                                       //本次成绩 
                    'cur_rank' => $this->getUserCurRank($u_id, $game_id, $curGameHistory, $monthStart,  $monthEnd)['rank'],      //本次排行
                ];
            }else{
                $msg = json_encode($curGameHistory->getErrors());
            }
            
        }else{
            $msg .= ($scene_id == null ?"scene_id 不能为空！" : "");
            $msg .= ($grade == null ?"grade 不能为空！" : "");
            $msg .= ($u_id == null ?"u_id 不能为空！" : "");
        }
        return [
            'code'=> $code,
            'data'=> $data,
            'msg'=> $msg,
        ];
    }
    
    /**
     * 获取是否为第一次登录
     * @param string $u_id
     * @param int $game_id
     * @return boolean 
     */
    private function getIsFirstLogin($u_id,$game_id){
        $result = (new Query())
                ->from(GameLoginHistory::tableName())
                ->where(['u_id'=>$u_id,'game_id'=>$game_id])
                ->count();
        return (int)$result > 1 ? 0 : 1;
    }
    
    /**
     * 查询用户最后一次登录时间
     * @param string $u_id  用户id
     * @param int $game_id 赛事id
     * 
     * @return int 
     */
    private function getUserLastLoginTime($u_id,$game_id){
        $result = (new Query())
                ->select(['created_at'])
                ->from(GameLoginHistory::tableName())
                ->where(['u_id'=>$u_id,'game_id'=>$game_id])
                ->orderBy('created_at DESC')
                ->one(Yii::$app->db);
        
        return ($result == null || count($result)==0) ? -1 : $result['created_at'] * 1000;
    }
    
     /**
     * 查询用户一共玩了多少次游戏
     * @param string $u_id 用户id
     * @param int $game_id 赛事id
     * 
     * @return int
     */
    private function getUserPlayNum($u_id,$game_id){
        $num = (new Query())
                ->from(GameHistory::tableName())
                ->where(['u_id'=>$u_id,'game_id'=>$game_id])
                ->count();
        return $num;
    }
    
    /**
     * 获取赛事成绩排行
     * @param type $game_id     赛事id
     * @param type $rank_num     前几名排行
     * @param int $start_time    开始时间
     * @param int $end_time      结束时间
     * 
     * @return array [[u_id,data,rank]...]
     */
    private function getGameRank($game_id,$rank_num=10,$start_time=null,$end_time=null){
        /**
         * 递增计算排行 
         **/
        $subQuery = (new Query())
                ->select([
                    "u_id","data",
                    "@curRank := IF(@PrevRank = data,@curRank,@incRank) AS rank",       //当前排名
                    "(@incRank := @incRank + 1)",                                       //成绩不同，增量排名
                    "(@prevRank := data)",                                              //记录当前成绩
                    ])
                ->from([
                    'GameHistory'=>  GameHistory::tableName(),
                    'Var'=>"(SELECT @curRank :=0,@prevRank := NULL, @INCrANK := 1)",    //初始化变量
                    ])
                ->where(['game_id'=>$game_id])
                ->andFilterWhere(['BETWEEN','created_at',  $start_time,  $end_time])
                ->orderBy('data DESC');
        
        /** 按用户分组，取目标前几名排行 */
        $query = (new Query)
                ->select(['u_id','User.nickname','User.avatar','MAX(data) as grade','rank'])
                ->leftJoin(['User'=>  User::tableName()], 'User.id = u_id')
                ->from(['sq' => $subQuery])
                ->where("rank <= $rank_num")
                ->groupBy(['u_id'])
                ->orderBy('data DESC');
        
        return $query->all(Yii::$app->db);       
    }
    
    /**
     * 获取用户最好记录
     * @param string $u_id
     * @param type $game_id             赛事id
     * @param int $start_time            开始时间
     * @param int $end_time              结束时间
     * 
     * @return array [u_id,data,rank]
     */
    private function getUserBestRank($u_id,$game_id,$start_time=null,$end_time=null){
        /**
         * 递增计算排行 
         **/
        $gameHistoryTableName = GameHistory::tableName();
        //查出目标用户的最高成绩，再查排名
        $bestGradeQuery = (new Query())
                ->select('data')
                ->from($gameHistoryTableName)
                ->where(['u_id'=>$u_id,'game_id'=>$game_id])
                ->limit(1);
        
        /* @var $subQuery Query */
        $subQuery = (new Query())
                ->select([
                    'u_id','data',
                    '@curRank:=IF(@PrevRank = data,@curRank,@incRank) AS rank',             //当前排名
                    '(@incRank:=@incRank+1)',                                               //成绩不同，增量排名
                    '(@prevRank:=data)',                                                    //记录当前成绩
                    ])
                ->from([
                    'GameHistory'=>  $gameHistoryTableName,
                    'VAR'=>'(SELECT @curRank :=0,@prevRank := NULL, @INCrANK := 1)',        //初始化变量
                    ])
                ->where(['game_id'=>(int)$game_id])
                ->andFilterWhere(['BETWEEN','created_at',  $start_time,  $end_time])
                ->andFilterWhere(['>=','data',  $bestGradeQuery])                           //查出目标用户的最高成绩，再查排名
                ->orderBy('data DESC');
        /** 获取目标用户排名后记录 */
        $query = (new Query)
                ->select(['u_id','MAX(data) as data','rank'])
                ->from(['sq'=>$subQuery])
                ->where(['u_id'=>$u_id]);
        
        return $query->one(Yii::$app->db);   
    }
    
    /**
     * 获取用户本次排行
     * @param string $u_id
     * @param int $game_id
     * @param GameHistory $history
     * @param int $start_time 开始时间
     * @param int $end_time   结束时间
     * @return array
     */
    private function getUserCurRank($u_id,$game_id,$history,$start_time=null,$end_time=null){
        /**
         * 递增计算排行 
         **/
        $gameHistoryTableName = GameHistory::tableName();
        
        /* @var $subQuery Query */
        $subQuery = (new Query())
                ->select([
                    'id','u_id','data',
                    '@curRank:=IF(@PrevRank = data,@curRank,@incRank) AS rank',             //当前排名
                    '(@incRank:=@incRank+1)',                                               //成绩不同，增量排名
                    '(@prevRank:=data)',                                                    //记录当前成绩
                    ])
                ->from([
                    'GameHistory'=>  $gameHistoryTableName,
                    'VAR'=>'(SELECT @curRank :=0,@prevRank := NULL, @INCrANK := 1)',        //初始化变量
                    ])
                ->where(['game_id'=>(int)$game_id])
                ->andFilterWhere(['BETWEEN','created_at',  $start_time,  $end_time])
                ->andFilterWhere(['>=','data',  floatval($history->data)])                            //比所查成绩要高的所有排名
                ->orderBy('data DESC');
        /** 获取目标用户排名后记录 */
        $query = (new Query)
                ->select(['id','u_id','MAX(data) as data','rank'])
                ->from(['sq'=>$subQuery])
                ->where(['id'=>$history->id]);
        
        return $query->one(Yii::$app->db);   
    }
    
    
    /**
     * 验证调用是否合法
     * $signature = md5($scene_id.$game_id.$rand.$token)
     * @return array [pass:boolean,msg:string] 
     */
    private function validateSign(){
        $post = Yii::$app->getRequest()->getQueryParams();
        $post = array_merge($post,Yii::$app->getRequest()->getBodyParams());
        
        $token = Yii::$app->params['WECHAT']['token'];
        $scene_id = isset($post['scene_id']) ? $post['scene_id'] : '';
        $game_id = isset($post['game_id']) ? $post['game_id'] : '';
        $time = isset($post['time']) ? $post['time'] : '';
        $signature = isset($post['signature']) ? $post['signature'] : '';
        
        if(time()*1000 - $time > 10*60*1000){
            return ['pass'=>false,'msg'=>'连接已过期!'];
        }
        
        if(empty($scene_id) || empty($game_id) ||empty($time) || empty($signature)){
            $msg = empty($scene_id) ? 'scene_id不能为空' : '';
            $msg .= empty($game_id) ? '$game_id不能为空' : '';
            $msg .= empty($time) ? '$time不能为空' : '';
            $msg .= empty($signature) ? '$signature不能为空' : '';
            return ['pass'=>false,'msg'=>$msg];
        }else{
            return ['pass'=>$signature == md5($scene_id.$game_id.$time.$token),'msg'=>'非法调用！'];
        }
    }
}
