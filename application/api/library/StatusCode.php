<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\api\library;

/**
 * Description of StatusCode
 *
 * @author Administrator
 */
class StatusCode
{
    const CODE_FAIL = 0;              //失败
    const CODE_SUCCESS = 200;         //成功
    const CODE_API_SIGN_EROOR = 40000;//apiSign错误
    const CODE_NOT_LOGIN = 40010;     //未登录
    const CODE_NO_AUTH = 40020;       //登录成功但是没有权限访问
    const CODE_LOGIN_ERROR = 40030;   //登录错误 
    const CODE_INVALID_PARAM = 40040; //参数有误
    const CODE_INVALID_HEADER_PARAM = 40041; //header参数有误
    const CODE_NOT_FOUND = 40050;     //资源不存在
    const CODE_MYSQL_ERROR = 40060;   //数据库操作失败
    
    const CODE_ORDER_SIGNED = 40070;   //订单已签收
    const CODE_NOT_FEEDBACK = 40071;   //订单尚未被反馈
    const CODE_INVALID_MONTH = 40072;   //查询月份无效
}
