//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    motto: '微信小程序支付demo',
    userInfo: {}
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad: function () {
    var that = this;
    //调用应用实例的方法获取全局数据
    app.getUserInfo(function(userInfo){
      //更新数据
      that.setData({
        userInfo:userInfo
      })
    })
  },
  showInfo: function (msg) { //错误信息提示
    wx.showModal({
      title: '提示',
      showCancel : false,
      content: msg
    });
  },
  wxpay: function () {
    var that = this;
    //登陆获取code
    wx.login({
      success: function (res) {
        //console.log(res);
        //获取openid
        that.getOpenId(res.code)
      }
    });
  },
  getOpenId: function (code) {
    var that = this;
    wx.request({
      url: "https://xxxxx/wx.mini.php",
      data: {
        type : 'openid',
        code : code
      },
      header: { //会将数据转换成 query string,即key=value
        'content-type':'application/x-www-form-urlencoded'
      },
      // header: { //会对数据进行 JSON 序列化
      //   'content-type':'application/json'
      // },
      method: 'POST', //默认为 GET，有效值：OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      success: function (res) {
        console.log(res)
        if (res.data.resultCode != 0){
          that.showInfo(res.data.errMsg);
          return;
        }
        that.generateOrder(res.data.openid)
      },
      fail: function () {
        // fail
      },
      complete: function (openid) {
        // complete
      }
    })
  },
  generateOrder: function (openid) {
    var that = this;
    wx.request({
      url: "https://xxxxx/wx.mini.php",
      data: {
        type: 'pay',
        openid : openid
      },
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: function (res) {
        //console.log(res);
        if (res.data.resultCode != 0) {
          that.showInfo(res.data.errMsg);
          return;
        }
        that.pay(res.data.params);
      },
      fail: function () {
        // fail
      },
      complete: function () {
        // complete
      }
    })
  },
  pay: function (param) {
    var that = this;
    wx.requestPayment({
      timeStamp: param.timestamp,
      nonceStr: param.nonce_str,
      package: param.package,
      signType: param.sign_type,
      paySign: param.pay_sign,
      success: function (res) {
        // success
        console.log(res);
        that.showInfo('支付成功');
      },
      fail: function (res) {
        // fail
        console.log(res);
        var strMsg = res.errMsg;
        if (res.err_desc){
          strMsg += ', ' + res.err_desc;
        }
        that.showInfo(strMsg);
      },
      complete: function () {
        // complete
        console.log("pay complete");
      }
    });
  }
})
