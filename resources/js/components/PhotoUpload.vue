<template>
    <div class="upload_wrap">
        <div class="title mb-3 d-flex align-items-center">
            <h5 class="fw-bold p-2">Картинка</h5>
            <a href="#" class="" style="font-size: 1.6rem;" @click.prevent="add"><i class="fas fa-circle-plus"></i></a>
        </div>

        <div class="photoUploadWrapItem mb-5" v-for="(item,index) in items">
            <div class="d-flex mb-3 justify-content-between">
                <input class="form-control "
                       type="file"
                       :ref="item.id"
                       @input="onFileChangeItem(item.id)"
                       accept=".png, .jpg, .jpeg"
                       :id="item.id">
                <a href="#" @click.prevent="del(index)" class="btn btn-sm btn-warning animate-up-2 ms-2 ">Удалить</a>
            </div>
            <div class="mb-2" v-show="item.previewImage!=''">
                <img :src="item.previewImage"
                     :id="item.id+'_gr'"
                     class="img-fluid">
            </div>
            <div class="">
                  <a class="btn btn-outline-primary" @click.prevent="addCanvas(index)">Добавить</a>
            </div>
        </div>
    </div>
</template>

<script>
import Cropper from 'cropperjs';
export default {
    name: "PhotoUpload",
    data() {
        return {
            items: []
        }
    },
    methods: {
        add() {
            let id = 'images_' + Date.now();
            this.items.push({
                previewImage: '',
                id: id,
                cropper:false,
                width:0,
                height:0
            });
        },
        onFileChangeItem(id) {
            let self = this;
            let input = document.getElementById(id);
            let file = input.files;
            let ind = -1;
            self.items.forEach((item, index) => {
                if (item.id == id) {
                    ind = index;
                }
            })
            if (file && file[0]) {
                let reader = new FileReader
                reader.onload = e => {
                    var img = new Image;
                    img.onload = function () {
                        if (ind != -1) {
                            // console.log(self.items[ind].id+'_gr');
                            const image = document.getElementById(self.items[ind].id+'_gr');
                            self.items[ind].cropper = new Cropper(image, {
                                crop(event) {
                                    // console.log(event.detail.x);
                                    // console.log(event.detail.y);
                                    // console.log(event.detail.width);
                                    // console.log(event.detail.height);
                                    self.items[ind].width=event.detail.width;
                                    self.items[ind].height=event.detail.height;
                                    // console.log(event.detail.rotate);
                                    // console.log(event.detail.scaleX);
                                    // console.log(event.detail.scaleY);
                                },
                            });
                        }
                    };
                    img.src = reader.result;
                    if (ind != -1) {
                        self.items[ind].previewImage = reader.result;
                    }
                }
                reader.readAsDataURL(file[0])
            }
        },
        del(index) {
            let item=this.items[index];
            this.$eventBus.emit('image-delete', item.id);
            this.items.splice(index, 1);
            let input = document.getElementById(item.id);
            input.value = null;
        },
        addCanvas(index){
            let blob = this.items[index].cropper.getCroppedCanvas().toDataURL();
            this.$eventBus.emit('image-change', {
                id: this.items[index].id,
                img: blob,
                width: this.items[index].width,
                height: this.items[index].height
            });
        }
    }
}
</script>

