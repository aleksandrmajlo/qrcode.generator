import { createStore } from "vuex";

export default createStore({
    state () {
        return {
            width: 60,
            heigth:40,
            fontSize:12,
            fontFamily:'Arial Regular',
            alingText:'left',
            qr:'/qrcode/default.png',
            img_default_width:50,

        }
    },
    mutations: {
        updateSize (state,type) {
           console.log(type)
        }
    }
})
