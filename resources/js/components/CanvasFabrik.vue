<template>
    <div>
        <div class="width_can d-flex align-items-center justify-content-center mb-5 mt-3">
            <a href="#" class="p-2" @click.prevent="minus('width_inp')">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <input type="number" v-model="width" min="1" ref="width_inp" step="1" readonly class="w-25 text-center">
            <a href="#" class="p-2" @click.prevent="plus('width_inp')">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
            <span class="p-2">mm</span>
        </div>
        <div class="d-flex heigth_can_can mb-5 justify-content-between align-items-start">
            <div class="canvas_wrap d-flex justify-content-center w-100">
                <canvas id="canvas" style="border:1px solid"></canvas>
            </div>
            <div class=" width_can d-flex flex-column justify-content-center align-items-center">
                <a href="#" @click.prevent="plus('heigth_inp')">
                    <i class="fa-solid fa-arrow-up"></i>
                </a>
                <input type="number" v-model="heigth" min="1" ref="heigth_inp" step="1" readonly
                       class="w-50 text-center">
                <a href="#" @click.prevent="minus('heigth_inp')">
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
                <span>mm</span>
            </div>
        </div>
        <div class="button_block_canvas">
            <div class="form-group mb-3 col-12">
                <label class="fw-bold">Шрифт,размер</label>
                <input type="number" v-model="fontSize" @input="fontSizeChange" class="form-control w-50">
                <!--                <small class="form-text  text-warning">Для выбора блока текста необходимо кликнуть по нему на полотне   (по умолчанию будет менятся размер шрифта последнего блока)</small>-->
            </div>
            <div class="form-group mb-3 col-12">
                <label class="fw-bold">Выравнивание</label>
                <select class="form-control w-50" v-model="alingText" @input="alingTextChange">
                    <option value="center">по центру</option>
                    <option value="left">слева</option>
                    <option value="right">справа</option>
                </select>
            </div>
            <div class="form-group mb-3 col-12">
                <label class="fw-bold">Шрифт</label>
                <select class="form-control w-50" v-model="fontFamily" @input="fontFamilyChange">
                    <option value="Arial Regular">Arial</option>
                    <!--                    <option value='Tahoma Regular font'>Tahoma</option>-->
                    <option value='Montserrat'>Montserrat</option>
                    <option value='Roboto'>Roboto</option>
                    </select>
            </div>
        </div>
<!--        <pre style="border:5px solid;display: block">{{ text }}</pre>-->
    </div>
</template>

<script>
import FontFaceObserver from 'fontfaceobserver'
import index from "vuex";

const mm_pixel = 2.835;
export default {
    name: "CanvasFabrik",
    data() {
        return {
            canvas: null,
            text: {},// текст на полотне
            textBlockActive: false,// активный блок
            fontSize: null,// размер шришта
            fontFamily: '',
            qr_image: null,// qr код изображение
            width: 0,
            heigth: 0,
            images: {},// изображения
            doc_xml: false,
            email: '',
        }
    },
    created() {
        this.width = this.$store.state.width
        this.heigth = this.$store.state.heigth
        this.fontSize = this.$store.state.fontSize;
        this.fontFamily = this.$store.state.fontFamily;
        this.alingText = this.$store.state.alingText;
        // изменение текста
        this.$eventBus.on('text-change', (datas) => {
            this.TextItemAddUpdate(datas);
        });
        //установить активным слой
        this.$eventBus.on('text-active-layer', (id) => {
            this.TextActiveLayer(id);
        });
        //удалить текст
        this.$eventBus.on('text-delete', (id) => {
            this.TextDeleteLayer(id);
        });
        //сгенерировать код при потере фокуса
        this.$eventBus.on('generate-qr', () => {
            this.generateQr();
        });

        // изображение добавляем
        this.$eventBus.on('image-change', (datas) => {
            this.ImgAddUpdate(datas);
        });
        // изображение удаляем
        this.$eventBus.on('image-delete', (id) => {
            this.removeImage(id);
        });
        // изменение документа
        this.$eventBus.on('file-change', (datas) => {
            if (datas.text) {
                this.doc_xml = datas.text;
            } else {
                this.doc_xml = false;
            }
            this.generateQr();
        });
        //генерация изображения
        this.$eventBus.on('suc_send', (datas) => {
            console.log('suc_send2')
            this.email = datas.email;
            this.generatePng();
        });
    },
    computed: {},
    mounted() {
        this.canvas = new fabric.Canvas("canvas", {
            width: this.width * mm_pixel,
            height: this.heigth * mm_pixel,
        });
        this.canvas.controlsAboveOverlay = true;
        // qr код по умолчанию
        fabric.Image.fromURL('/qrcode/default.png', (image) => {
            image.scaleToHeight(50);
            image.scaleToWidth(50);
            let position = this.QrCodeRightCenter(50, 50);
            image.set({
                left: position.left,
                top: position.top,
                centeredScaling: true,
            });
            this.canvas.add(image);
            this.qr_image = image;
        })
        this.canvas.renderAll();
    },
    methods: {
        minus(ref) {
            let input = this.$refs[ref];
            let v = Number(input.value);
            if (v > input.min) {
                let r = v - 1;
                input.value = r;
                if (ref == 'width_inp') this.width = r;
                else this.heigth = r;
                this.UpdateSizeCanvas();
                // передаем данные в хранилище
                this.$store.commit("updateSize", {
                    width: this.width,
                    heigth: this.heigth,
                });
            }
        },
        plus(ref) {
            let input = this.$refs[ref];
            let v = Number(input.value);
            let r = v + 1
            input.value = r;
            if (ref == 'width_inp') this.width = r;
            else this.heigth = r;
            this.UpdateSizeCanvas();
            // передаем данные в хранилище
            this.$store.commit("updateSize", {
                width: this.width,
                heigth: this.heigth,
            });
        },

        // обновления размера полотна
        UpdateSizeCanvas() {
            this.canvas.setHeight(this.heigth * mm_pixel);
            this.canvas.setWidth(this.width * mm_pixel);
            this.canvas.renderAll()
        },
        // установка qr кода справа по средине
        QrCodeRightCenter(width, height) {
            let heigth_middle = (this.canvas.height - height) / 2;
            let rigth = this.canvas.width - width;
            return {
                top: heigth_middle,
                left: rigth
            }
        },
        //обновление одной строк
        TextItemAddUpdate(datas) {
            let item = datas.item;
            let id = item.id;
            let indent = mm_pixel * datas.indent;
            //обновляем элемент
            if (this.text[id] != undefined) {
                let text = this.text[id];
                text.set('text', item.text);
            } else {
                //добавляем элемент
                let text = new fabric.Text(item.text, {
                    left: datas.left * mm_pixel,
                    fontSize: this.$store.state.fontSize,
                    fontFamily: this.$store.state.fontFamily,
                    textAlign: this.$store.state.alingText,
                    id_my: id
                });
                this.text[id] = text;
                /*
                *вировнять по вертикали
                * тут мы добавляем и элемент последний
               */
                let top = 0;
                for (const [key, value] of Object.entries(this.text)) {
                    if (key != id) {
                        top += value.height + indent;
                    }
                }
                text.top = top + indent;
                this.canvas.add(text);
                text.setControlsVisibility({
                    bl: false,
                    br: false,
                    tl: false,
                    tr: false,
                    mt: false,
                    mb: false,
                    ml: false,
                    mr: false,
                    mtr: false
                });
                this.canvas.setActiveObject(text);
                this.canvas.renderAll();

                this.textBlockActive = id;
                this.alingText = text.textAlign;
                this.fontSize = text.fontSize;
                this.fontFamily = text.fontFamily;
                // console.log(this.textBlockActive + ' active')
                // console.log(text.top + ' top')
                text.on('mousedown', () => {
                    this.textBlockActive = id;
                    this.alingText = text.textAlign;
                    this.fontSize = text.fontSize;
                    this.fontFamily = text.fontFamily;

                });
            }
        },
        TextDeleteLayer(id) {
            if (typeof this.text[id] != 'undefined') {
                let object = this.text[id];
                this.canvas.remove(object);
                delete this.text[id];
                this.canvas.renderAll();
            }
        },
        // изменние размера шрифта
        fontSizeChange() {
            if (typeof this.text[this.textBlockActive] != 'undefined') {
                this.text[this.textBlockActive].fontSize = this.fontSize;
                this.canvas.renderAll();
            }
        },
        //изменение выравнивания шрифат
        alingTextChange() {
            setTimeout(() => {
                if (typeof this.text[this.textBlockActive] != 'undefined') {
                    this.text[this.textBlockActive].textAlign = this.alingText;
                    this.canvas.renderAll();
                }
            }, 10);
        },
        fontFamilyChange() {
            setTimeout(() => {
                if (typeof this.text[this.textBlockActive] != 'undefined') {
                    var font = this.fontFamily;
                    var myfont = new FontFaceObserver(font);
                    // console.log(font + ' font change')
                    myfont.load()
                        .then(() => {
                            this.text[this.textBlockActive].set("fontFamily", font);
                            this.canvas.requestRenderAll();
                        }).catch((e) => {
                        console.info('fontError')
                        console.log(e)
                    });
                }
            }, 10);
        },
        TextActiveLayer(id) {
            if (typeof this.text[id] != 'undefined') {
                this.textBlockActive = id;
                this.canvas.setActiveObject(this.text[id]);
                this.canvas.renderAll();
                this.alingText = this.text[id].textAlign;
                this.fontSize = this.text[id].fontSize;
                this.fontFamily = this.text[id].fontFamily;
            }
        },
        // добавление фото
        ImgAddUpdate(datas) {
            //  images
            // console.log(datas)
            let id = datas.id;
            //обновляем элемент
            if (this.images[id] != undefined) {

            }
            else{

                let pr = datas.width / datas.height;// пропорция размеров
                let width = this.$store.state.width;
                let height = width / pr;
                let position = this.QrCodeRightImages(id,width, height);
                let scale = this.$store.state.width / datas.width;
                // console.log(position,' position');
                fabric.Image.fromURL(datas.img, (image) => {
                    image.set({
                        centeredScaling: true,
                        left: 0,
                        top: position.top,
                        // scale:scale,
                        scaleX: scale,
                        scaleY: scale
                    });

                    this.canvas.add(image);
                    this.canvas.setActiveObject(image);
                    this.canvas.requestRenderAll();
                    this.images[id] =image;

                });
            }


        },
        //изображение удаляем
        removeImage(id){
            if (typeof this.images[id] != 'undefined') {
                let object = this.images[id];
                this.canvas.remove(object);
                delete this.images[id];
                this.canvas.renderAll();
            }
        },
        // изображение справа позиция
        QrCodeRightImages(id,width, height){
            let top_middle = 0;
            Object.keys(this.images).forEach(key => {
                if(key!=id){
                    let item=this.images[key];
                    let height=item.height*item.scaleX;
                    top_middle+=height;
                    console.log(item.height +' height')
                    console.log(height +' height2')
                    console.log(item.scaleX +' scaleX')
                }
            });
            let rigth = this.canvas.width - width;
            return {
                top: top_middle,
                left: rigth
            }
        },
        // генерация qr
        generateQr() {
            let text = '';
            let self = this;
            if (this.doc_xml) {
                text = this.doc_xml;
            } else {
                let objectLength = Object.keys(this.text).length;
                console.log(objectLength+' objectLength')
                if (objectLength > 0) {
                    for (const [key, value] of Object.entries(this.text)) {
                        text += value.text + '\n';
                    }
                } else {
                    text = 'QR codes';
                }
            }
            axios.post('/text_qr', {
                text: text
            })
                .then(res => {
                    var img = new Image();
                    img.onload = () => {
                        this.qr_image.setElement(img);
                        this.qr_image.setSrc(
                            res.data.qr,
                            function () {
                                self.canvas.renderAll()
                            }
                        );
                        self.canvas.renderAll();
                    }
                    img.src = res.data.qr;
                })
                .catch(err => {
                    console.error(err);
                })
                .then(() => {
                })
        },

        generatePng() {
            const dataURL = this.canvas.toDataURL({
                width: this.canvas.width,
                height: this.canvas.height,
                left: 0,
                top: 0,
                format: 'png',
            });
            // const link = document.createElement('a');
            // link.download = 'qr.png';
            // link.href = dataURL;
            // console.log(dataURL)
            // document.body.appendChild(link);
            // link.click();
            // document.body.removeChild(link);

            let data = new FormData();
            // canvas.toBlob(function (blob) {
            data.append('img', dataURL);
            data.append('email', this.email);

            axios
                .post(`/send`, data, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                .then(res => {
                    // console.log(res)
                });
        }
    }
}
</script>
