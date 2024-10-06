# GoEdgeEPay 插件

此插件用于对接 GoEdge CDN 系统中的易支付系统。

## 功能

- 支持支付宝和微信支付
- 处理异步和同步支付通知
- 验证签名

## 使用步骤

1. 将 `config.php` 文件中的 `api_url`、`api_pid` 和 `api_key` 替换为您的易支付信息。
2. 使用 `GoEdgeEPay::callPaymentApi` 发起支付。
3. 使用 `notify.php` 和 `return.php` 处理支付结果通知。

## 注意事项

1. 此插件并非直接放在GoEdge系统里，你需要单独创建一个php网站用于此插件的运作。
2. 此插件仅为对接第三/四方支付插件，支付不做推荐，不承担任何责任。
