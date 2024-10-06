<?php
$config = include('config.php');
$pid = $_GET['pid'] ?? '';
$trade_no = $_GET['trade_no'] ?? '';
$out_trade_no = $_GET['out_trade_no'] ?? '';
$type = $_GET['type'] ?? '';
$name = $_GET['name'] ?? '';
$money = $_GET['money'] ?? '';
$trade_status = $_GET['trade_status'] ?? '';
$sign = $_GET['sign'] ?? '';
$sign_type = $_GET['sign_type'] ?? 'MD5';
// 验证签名
if (validateSign($_GET, $config['api_key'])) {
    if ($trade_status == 'TRADE_SUCCESS') {
        // 处理支付成功的逻辑
        // 更新订单状态等
        echo 'success'; // 返回success表示接收成功
    } else {
        echo 'fail'; // 处理失败
    }
} else {
    echo 'Invalid sign'; // 签名不合法
}

// 验证签名的函数
function validateSign($params, $key) {
    global $config;
    $sign = generateSign($_GET,$config['api_key']);
    if($sign === $_GET['sign']){
        echo set_order_success($_GET['out_trade_no']);
        return true;
    }else{
        return false;
    }
}
function generateSign($params, $key) {
    // 1. 按照参数名的ASCII码排序
    ksort($params);
    // 2. 拼接成键值对的字符串
    $signStr = '';
    foreach ($params as $k => $v) {
        if ($k != "sign" && $k != "sign_type" && $v != '') {
            $signStr .= $k . '=' . $v . '&';
        }
    }
    // 3. 将密钥拼接到字符串后
    $signStr = substr($signStr,0,-1);
    $signStr .= $key;

    // 4. MD5加密并返回小写签名
    return md5($signStr);
}
function set_order_success($trade_no){
    global $config;
    $auth_info = [
        "type"=> "admin",
        "accessKeyId"=> $config['goedge_access_id'],
        "accessKey"=> $config['goedge_access_key']
    ];
    $access_token_json = sendPostJson($config['goedge_api_url']."/APIAccessTokenService/getAPIAccessToken",json_encode($auth_info,true),"none");
    $access_token = json_decode($access_token_json,true);
    $token = $access_token['data']['token'];
    $code = [
        "code"=>$trade_no
    ];
    $finish_order = sendPostJson($config['goedge_api_url']."/UserOrderService/finishUserOrder",json_encode($code,true),$token);
    return $finish_order;
}

function sendPostJson($url, $json,$token) {
    // 初始化cURL
    $ch = curl_init($url);

    // 设置cURL选项
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',  // 设置请求头为JSON
        'Content-Length: ' . strlen($json),  // 设置请求体长度
        'X-Edge-Access-Token: ' . $token
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  // 传递JSON数据

    // 执行请求并获取响应
    $response = curl_exec($ch);

    // 错误处理
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    // 关闭cURL会话
    curl_close($ch);

    // 返回响应
    return $response;
}
?>