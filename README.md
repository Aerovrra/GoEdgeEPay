# GoEdgeEPay 插件

此插件用于对接 GoEdge CDN 系统中的易支付系统。

## 功能

- 支持支付宝和微信支付
- 处理异步和同步支付通知
- 验证签名

## 参考文档

- https://goedge.cloud/docs/Developer/Pay.md
- https://goedge.cloud/dev/api/service/UserOrderService#findEnabledUserOrder
- https://goedge.cloud/dev/api/service/UserOrderService#finishUserOrder
- https://goedge.cloud/docs/API/Summary.md
  
## 使用步骤

1. 将 `config.php` 文件中的 `api_url`、`api_pid` 和 `api_key` 替换为您的易支付信息。
2. 将 `config.php` 文件中的 `godege_api_url` `goedge_access_id` `goedge_access_key` 替换为您的CDN系统信息。
3. 使用 `GoEdgeEPay::callPaymentApi` 发起支付。
4. 使用 `notify.php` 和 `return.php` 处理支付结果通知。

## 注意事项

1. 此插件并非直接放在GoEdge系统里，你需要单独创建一个php网站用于此插件的运作。
2. GoEdge API节点设置参考 https://goedge.cloud/docs/API/Settings.md
3. GoEdge AccessID & AccessKey 参考 "从v0.2.3开始，如果你还没有管理员AccessKey，则需要在”系统用户” – 用户”详情” – “API AccessKey”中创建。" #https://goedge.cloud/docs/API/Auth.md
4. 此插件仅为对接第三/四方支付插件，支付不做推荐，不承担任何责任。
