# GoEdgeEPay 插件

此插件用于对接 GoEdge CDN 系统中的易支付系统。

## 功能

- 支持支付宝和微信支付
- 处理异步和同步支付通知
- 验证签名

## 使用步骤

1. 将 `config.php` 文件中的 `api_url`、`api_pid` 和 `api_key` 替换为您的易支付信息。
2. 使用 `GoEdgeEPay::createPayment` 方法发起支付。
3. 使用 `notify.php` 和 `return.php` 处理支付结果通知。
