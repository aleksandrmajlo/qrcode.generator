<template>
    <div class="text_wrap">
        <div class="title d-flex align-items-center">
            <h5 class="fw-bold p-2">Строки</h5>
            <a href="#" class="" style="font-size: 1.6rem;" @click.prevent="add"><i class="fas fa-circle-plus"></i></a>
        </div>
        <div>
            <small  class="form-text  text-warning">Увелечение ширины доступно пробелами</small>
        </div>
        <div class="item mb-3 d-flex align-items-center" v-for="(item,index) in items">
            <textarea @click="setActive(index)" v-model="item.text"
                      @input="textChange(index)"
                      @blur="generateQr"
                      class="form-control m-2"></textarea>
            <a href="#"  @click.prevent="remove(index)"><i class="fas fa-trash-can"></i></a>
        </div>
<!--        <pre>{{items}}</pre>-->
        <div class="mb-3 form-inline ">
            <div class="form-group">
                <label>Отступ между строками по вертикали(мм)</label>
                <input type="number" min="1" class="form-control"  v-model="indent">
            </div>
        </div>
        <div class="mb-3 form-inline ">
            <div class="form-group">
                <label>Отступ слева(мм)</label>
                <input type="number" min="1" class="form-control"  v-model="left">
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "TextChange",
    data(){
        return {
            indent:2,
            left:1,
            items:[

            ]
        }
    },
    created() {
        //добавить текст с csv
        this.$eventBus.on('add_csv', (datas) => {
            console.log(datas)
            this.addCsv(datas)
        });
    },
    methods :{
        add(){
            let id='id_'+Date.now();
            this.items.push({
                text:'',
                id:id
            });
        },
        remove(index){
            let item=this.items[index];
            this.$eventBus.emit('text-delete', item.id);
            this.items.splice(index, 1);
        },
        // установить активным слой
        setActive(index){
            let item=this.items[index];
            this.$eventBus.emit('text-active-layer',item.id );
        },
        //измение текста
        textChange(index){
            let item=this.items[index];
            this.$eventBus.emit('text-change', {item:item,indent:this.indent,left:this.left});
        },
        generateQr(){
            this.$eventBus.emit('generate-qr');
        },
        // добавить текст с цсв
        addCsv(datas){
            // очищаем слой
            this.items.forEach(item=>{
                this.$eventBus.emit('text-delete', item.id);
            })
            this.items=[];
            datas.forEach((item,key)=>{
                let id='id_'+Date.now();
                this.items.push({
                    text:item,
                    id:id+'-'+key
                });
            })
            this.items.forEach(item=>{
                this.$eventBus.emit('text-change', {item:item,indent:this.indent,left:this.left});
            })

        }
    }
}
</script>

