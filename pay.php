<?php
// 接收Goedge传递的参数
$config = include('config.php');
$orderMethod = $_GET['EdgeOrderMethod'] ?? '';
$orderCode = $_GET['EdgeOrderCode'] ?? '';
$orderAmount = $_GET['EdgeOrderAmount'] ?? '';
$orderTimestamp = $_GET['EdgeOrderTimestamp'] ?? '';
$orderSign = $_GET['EdgeOrderSign'] ?? '';

// 校验接收到的参数
if (empty($orderCode) || empty($orderAmount) || empty($orderSign)) {
    die("Invalid request.");
}
auth();

// 验证签名逻辑可以根据需要进行添加

// 发起支付请求
callPaymentApi($orderCode, $orderAmount,$orderMethod);
function auth()
{
    global $config,$orderCode;
    $auth_info = [
        "type"=> "admin",
        "accessKeyId"=> $config['goedge_access_id'],
        "accessKey"=> $config['goedge_access_key']
    ];
    $access_token_json = sendPostJson($config['goedge_api_url']."/APIAccessTokenService/getAPIAccessToken",json_encode($auth_info,true),"none");
    $access_token = json_decode($access_token_json,true);
    $token = $access_token['data']['token'];
    $code = [
        "code"=>$orderCode,
    ];
    $status = sendPostJson($config['goedge_api_url']."/UserOrderService/findEnabledUserOrder",json_encode($code,true),$token);
    if ($status['code'] != 200 or $status['data']['userOrder']['code'] != $orderCode) {
        exit("验证失败");
    }
}
function callPaymentApi($orderCode, $orderAmount,$type) {
    // 商户ID、密钥
    global $config;
    $merchant_id = $config['api_pid']; // 请替换为您的商户ID
    $merchant_key = $config['api_key']; // 替换为真实密钥
    
    // 支付接口URL
    $api_url = $config['api_url']."/submit.php";
    // 请求参数
    $params = [
        'pid' => $merchant_id,
        'type' => $type, // 支付方式，您可以选择 'wxpay' 或其他方式
        'out_trade_no' => $orderCode, // 商户订单号
        'notify_url' => "http://yourdomain.com".'/notify.php', // 异步通知地址 请注意这里应该改为当前网站！！
        'return_url' => "http://yourdomain.com".'/return.php', // 页面跳转通知地址 请注意这里应该改为当前网站！！
        'name' => 'VIP会员', // 商品名称
        'money' => $orderAmount, // 订单金额
        'sign_type' => "MD5"
    ];
    
    // 生成签名
    $params['sign'] = generateSign($params, $merchant_key);

    // 发起POST请求
    $response = sendPostRequest($api_url, $params);

    // 解析返回的JSON数据
    $result = json_decode($response, true);
    echo "<html><body onload='document.forms[0].submit()'>";
    echo "<form action='{$api_url}' method='post'>";
    foreach ($params as $key => $value) {
        echo "<input type='hidden' name='{$key}' value='{$value}'>";
    }
    echo "</form></body></html>";
}

// 生成签名的函数
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
// 发送POST请求的函数
function sendPostRequest($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
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
