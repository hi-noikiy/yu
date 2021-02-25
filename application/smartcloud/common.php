<?php

/**
 * 微信展示时间的方法
 * @param $addTime
 * @return string
 */
function getChatTimeStr($addTime) {
    $nowTime = time();

    if($addTime > $nowTime) {
        return "";
    }

    //返回的时间
    $timeStr = "";
    //获取当前时间
    $addTime = explode(',', date('Y,n,j,w,a,h,i,y', $addTime));//年，月，日，星期，上下午，时，分
    $nowTime = explode(',', date('Y,n,j,w,a,h,i,y', $nowTime));
    if($addTime[4] == "am") {
        $ap = "上午";
    } else if($addTime[4] == "pm") {
        $ap = "下午";
    }
    $dayPerMonthAddTime = getDayPerMonth($addTime[0]);
    $week = array(0=>"星期日",1=>"星期一",2=>"星期二",3=>"星期三",4=>"星期四",5=>"星期五",6=>"星期六");
    if($addTime[0] == $nowTime[0] && $addTime[1] == $nowTime[1] && $addTime[2] == $nowTime[2]) {
        //如果时间差小于一天的,显示（上午 时间） / （下午 时间）
        $timeStr .= $ap . " " .$addTime[5] . ":" . $addTime[6];
    } else if(($addTime[0] == $nowTime[0] && $addTime[1] == $nowTime[1] && $addTime[2] == $nowTime[2]-1)
        || ($addTime[0] == $nowTime[0] && $nowTime[1]-$addTime[1] == 1 && $dayPerMonthAddTime[$addTime[1]] == $addTime[2] && $nowTime[2] == 1)
        || ($nowTime[0]-$addTime[0] == 1 && $addTime[1] == 12 && $addTime[2] == 31 && $nowTime[1] == 1 && $nowTime[2] == 1)) {
        //如果时间差在昨天,三种情况（同一月份内跨一天、月末跨越到月初、年末跨越到年初）显示格式：昨天
        $timeStr .= "昨天";
    } else if(($addTime[0] == $nowTime[0] && $addTime[1] == $nowTime[1] && $nowTime[2] - $addTime[2] < 7)
        || ($addTime[0] == $nowTime[0] && $nowTime[1]-$addTime[1] == 1 && $dayPerMonthAddTime[$addTime[1]]-$addTime[2]+$nowTime[2] < 7
            || ($nowTime[0]-$addTime[0] == 1 && $addTime[1] == 12 && $nowTime[1] == 1 && 31-$addTime[2]+$nowTime[2] < 7))) {
        //如果时间差在一个星期之内的,也是三种情况，显示格式：星期
        $timeStr .= $week[$addTime[3]];
    } else {
        //显示格式：年/月/日
        $timeStr .= $addTime[0] . "/" . $addTime[1] . "/" . $addTime[2];
    }
    return $timeStr;
}

//根据年份获取每个月份的总天数和每年最后一个月的天数
function getDayPerMonth($year) {
    $arr = array(
        1 => 31,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31
    );
    //闰年
    if(($year%4==0&&$year%100!=0) || ($year%400==0)) {
        $arr[2] = 29;
    } else {
        $arr[2] = 28;
    }
    return $arr;
}

/**
 * 表情转换（进行编码）
 * @param $nickname
 * @return string
 * 解码 rawurldecode($nickname)
 */
function emoji_encode($nickname){
    $strEncode = '';
    $length = mb_strlen($nickname,'utf-8');
    for ($i=0; $i < $length; $i++) {
        $_tmpStr = mb_substr($nickname,$i,1,'utf-8');
        if(strlen($_tmpStr) >= 4){
            $strEncode .= rawurlencode($_tmpStr);
        }else{
            $strEncode .= $_tmpStr;
        }
    }
    return $strEncode;
}
