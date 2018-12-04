var a = getApp();

Page({
    data: {
        setting: [],
        shareinfo: [],
        app_uid: "",
        parentid: "",
        url: "",
        show_app: !1,
        show_btn: !1
    },
    onLoad: function(a) {
        var t = this;
        a.app_uid && t.setData({
            parentid: a.app_uid
        }), setTimeout(function() {
            t.load_shareinfo(), t.load_index();
        });
    },
    save_recommend: function(t) {
        var e = this;
        a.util.request({
            url: "entry/wxapp/saveRecommend",
            data: {
                wxapp_uid: t,
                parentid: e.data.parentid
            },
            success: function(a) {}
        });
    },
    load_index: function() {
        var t = this;
        a.util.request({
            url: "entry/wxapp/index",
            success: function(a) {
                t.setData({
                    url: a.data.data
                });
            }
        });
    },
    load_shareinfo: function() {
        var t = this, e = "", n = wx.getStorageSync("userInfo");
        if (n.memberInfo) e = n.memberInfo.uid;
        a.util.request({
            url: "entry/wxapp/shareinfo",
            data: {
                wxapp_uid: e
            },
            success: function(a) {
                if (t.setData({
                    setting: a.data.data.setting,
                    shareinfo: a.data.data.shareinfo,
                    app_uid: a.data.data.app_uid
                }), a.data.data.sharewxapp) wx.navigateTo({
                    url: "../newlesson/index"
                }); else {
                    if (wx.getStorageSync("userInfo")) return t.setData({
                        show_app: !0
                    }), !1;
                    t.setData({
                        show_btn: !0
                    });
                }
                wx.setNavigationBarTitle({
                    title: "微信授权"
                });
            }
        });
    },
    onShareAppMessage: function(a) {
        var t = this;
        return {
            title: t.data.shareinfo.title,
            path: "/fy_lessonv2/pages/index/index?app_uid=" + t.data.app_uid,
            imageUrl: t.data.shareinfo.images,
            success: function(a) {
                wx.showToast({
                    title: "分享成功(" + t.data.app_uid + ")",
                    icon: "success",
                    duration: 2e3
                });
            },
            fail: function(a) {}
        };
    },
    updateUserInfo: function(t) {
        var e = this;
        a.util.getUserInfo(function(a) {
            a.memberInfo && (e.load_shareinfo(), e.save_recommend(a.memberInfo.uid)), e.setData({
                show_app: !0
            });
        }, t.detail);
    }
});