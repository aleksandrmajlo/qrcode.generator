<template>
<div style="display: none;">
    <div id="modal-subscribe">
        <h3 class="text-center">Подписка</h3>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Email</label>
            <input type="email" v-model="email" class="form-control  "  v-bind:class="{'is-invalid': err }" id="exampleFormControlInput1" placeholder="name@example.com">
            <div class="invalid-feedback">
               Укажите коректный email
            </div>
        </div>
        <button  :disabled="disabled" type="button" @click.prevent="send" class="btn btn-sm btn-warning animate-up-2">Получить</button>
        <p class="text-success mt-3" v-if="suc">
            На указанную Вами почту отправлен QRCode
        </p>
    </div>
</div>
</template>

<script>
export default {
    name: "FormSub",
    data() {
        return {
            disabled:false,
            suc:false,
            err:false,
            email:''
        }
    },
    methods: {
        send(){
            console.log(this.ValidateEmail()+'valid')
            if(this.ValidateEmail()){
                this.disabled=true;
                this.err=false;
                setTimeout(()=>{
                    this.email='';
                    this.suc=true;
                    this.disabled=false;
                },2000);
                setTimeout(()=>{
                    this.suc=false;
                },7000);
                this.$eventBus.emit('suc_send',{email:this.email } );
            }else{
                this.err=true;
                console.log(this.err+' err')
            }
        },
        ValidateEmail() {
            var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            if (this.email.match(validRegex)) {
                return true;
            } else {
                return false;
            }
        }

    },
}
</script>

<style scoped>
#modal-subscribe{
    min-width: 450px;
}

</style>
