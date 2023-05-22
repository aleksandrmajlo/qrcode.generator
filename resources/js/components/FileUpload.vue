<template>
    <div class="upload_wrap">
        <div class="title mb-3">
            <h5 class="fw-bold p-2">CSV XLX</h5>
        </div>
        <div class="d-flex">
            <div class="mb-3">
                <input class="form-control" type="file"
                       accept=".csv,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                       ref="fileInput"
                       @input="onFileChange"
                >
            </div>
        </div>
        <div class="" v-show="previewImage!=''">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <!--                <a href="#" @click.prevent="save" class="btn btn-sm btn-warning animate-up-2">Добавить</a>-->
                <a href="#" @click.prevent="del" class="btn btn-sm btn-warning animate-up-2">Удалить</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FileUpload",
    data() {
        return {
            previewImage: ""
        }
    },
    methods: {
        onFileChange() {
            let input = this.$refs.fileInput
            let file = input.files
            if (file && file[0]) {
                this.previewImage=file[0];
                let reader = new FileReader
                reader.onload = e => {
                    // this.previewImage = e.target.result;
                    var formData = new FormData();
                    formData.append("file", file[0]);
                    axios.post('upload_file', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then((res) => {
                            console.log(res.data)
                            //генерация кода
                            this.$eventBus.emit('file-change',{text:res.data.qr } );
                            // добавить текст
                            this.$eventBus.emit('add_csv',res.data.text  );
                            /*
                            this.$eventBus.emit('file-change',{text:res.data.qr } );
                            this.$eventBus.emit('text-change', {
                                item:{
                                    text:res.data.text,
                                    id:id
                                },
                                indent:5,
                                left:1
                            });
                            */

                        })
                        .catch(function () {
                            console.log('FAILURE!!');
                        });

                }
                reader.readAsDataURL(file[0])
            }
        },
        del() {
            let input = this.$refs.fileInput;
            input.value = null;
            this.previewImage = '';
            this.$eventBus.emit('file-change',{text:false } );
        }
    }
}
</script>

<style scoped>

</style>
