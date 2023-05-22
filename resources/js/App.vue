<template>
    <div class="container mb-5">
        <h1 class="text-center mb-5">QR Code Generator</h1>
        <div class="row">
            <div class="col-md-8 ">
                <div id="canvas_wrap" class="shadow-soft p-3">
                    <canvas-fabrik></canvas-fabrik>
                </div>
            </div>
            <div class="col-md-4">
                <div class="shadow-soft p-3">
                    <text-change></text-change>
                    <hr/>
                    <photo-upload></photo-upload>
                    <hr/>
                    <file-upload></file-upload>
                    <hr/>
                    <div class="button_wrap d-flex align-items-center justify-content-around">
                        <a data-fancybox href="#modal-subscribe" class="btn btn-sm btn-warning animate-up-2"><i class="fas fa-download mr-2"></i>Download</a>
                    </div>
                </div>
            </div>
        </div>
        <FormSub></FormSub>
    </div>
</template>
<script>


import FormSub from './components/modal/FormSub.vue';
import TextChange from "./components/TextChange.vue";
import PhotoUpload from "./components/PhotoUpload.vue";
import FileUpload from "./components/FileUpload.vue";
import CanvasFabrik from "./components/CanvasFabrik.vue";
export default {
    name: "App",
    data(){
        return{

        }
    },
    components: {
        FormSub,
        TextChange,
        PhotoUpload,
        FileUpload,
        CanvasFabrik
    },
    created() {


    }
}
</script>
