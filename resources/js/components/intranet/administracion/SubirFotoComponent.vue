<template>
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <form class="form-inline">
                <div class="form-group mx-sm-2 mb-2">
                    <input type="text" class="form-control" v-model="dni" id="inputPassword2" placeholder="Ingrese número de documento">
                </div>
                <button type="button" class="btn btn-primary mb-2" @click="getEstudiante">Buscar</button>
                <div class="container row">
                    <div class="col-xs-12 col-md-12">
                        <span :class=" errorDni != '' ? 'text-danger' : ''">{{ errorDni }}</span>
                    </div>
                </div>

            </form>
            <div class="text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <h6>{{ nombresCompletos }}</h6>
                            <img :src="foto != '' ? '../../storage/fotos/'+foto : ''" class="card-img border-right img-fluid" alt="">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xs-12 col-md-8" style="border-left:1px solid #ddd">
            <div class="form-group">
                <div class="input-group">
                    <div class="custom-file">
                    <input type="file" id="image_data" name="image" @change="GetImage" class="custom-file-input ">
                    <label class="custom-file-label" for="exampleInputFile" >1. Selecionar Imagen</label>
                    </div>
                </div>

            </div>
            <div class="">
                <div class="row">
                    <div class="col-md-4">
                        <label>2. Recorta</label>

                        <cropper
                            class="cropper"
                            :src="avatar"
                            ref="cropper"
                            :auto-zoom="true"
                            :transitions="true"
                            :resize-image="{
                                adjustStencil: true
                            }"
                            image-restriction="fit-area"
                            default-boundaries="fill"
                            :stencil-props="{
                                aspectRatio: 10/12
                            }"
                            :min-height="limitations.minHeight"
                            :min-width="limitations.minWidth"
                            :size-restrictions-algorithm="pixelsRestriction"
                            @change="change"
                        />
                    </div>
                    <div class="col-md-4">
                        <label>3. Previsualiza el resultado</label>
                        <div v-if="image">
                            <img :src="image" class="img-fluid" alt="Responsive image">
                        </div>

                    </div>
                    <div class="col-md-4">
                        <label for="">Ancho:{{coordinates.width}} <b id="ancho"></b>px</label>
                        <br>
                        <label for="">Alto:{{coordinates.height}} <b id="alto"></b>px</label>
                        <div class="alert alert-info" role="alert">
                            <small><li>El recorte de la imagen debe ser superior a 413 px de ancho y 531 px de alto</li></small>
                        </div>
                        <div v-if="modal" class="alert alert-danger mt-0" role="alert">
                        <!-- <div class="alert alert-danger mt-0" role="alert"> -->
                            <small><li>El recorte de la imagen es muy pequeña</li></small>
                        </div>
                        <span class="badge badge-danger" id="error_imagen"></span>
                        <br>
                        <button v-if="foto != ''" type="button" v-on:click="guardar()" class="btn btn-primary">Actualizar Foto</button>
                    </div>

                    <div class="col-md-3 offset-md-4 col-xs-12 form-group">
                        <div id="show" v-if="img" style="display:none">
                                <canvas class="img-fluid img-thumbnail" id="canvas" ></canvas>
                        </div>
                    </div>
                </div>
            </div><br>

        </div>

    </div>
</template>
<script>
import axios from "axios";
import { Cropper } from 'vue-advanced-cropper';
import { ResizeEvent } from 'vue-advanced-cropper';
import $ from 'jquery'
import toastr from "toastr";

export default {
    components: {
		Cropper,
	},
    data(){
        return{
            image:null,
            img:false,
            avatar:null,
            final:null,
            fin:null,
            modal: false,
            dni:'',
            foto:'',
            nombres:'',
            paterno:'',
            materno:'',
            id:0,
            coordinates: {
				width: 0,
				height: 0,
				left: 0,
				top: 0,
            },
            limitations: {
				minWidth: 412,
                minHeight: 430,
                maxWidth: 413 +50 ,
				maxHeight: 531 +50,
			},
            errorDni:''
        }
    },
    methods:{
        pixelsRestriction({ minWidth, minHeight, maxWidth, maxHeight, imageWidth, imageHeight }) {
			return {
				minWidth: minWidth,
				minHeight: minHeight,
				maxWidth: maxWidth,
				maxHeight: maxHeight,
			};
		},
        GetImage(e){

            let image = e.target.files[0]
            let reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onload = e =>{
                this.avatar = e.target.result
                //this.NuevoRedic(this.avatar, 413, 531)
            }
        },
        cerrar(){

            this.avatar = null
            this.image = null
            this.coordinates.width = null
            this.coordinates.height = null
            this.coordinates.top = null
            this.coordinates.left = null

        },
        guardar(){
            var idEstudiante = this.id
            const { canvas } = this.$refs.cropper.getResult();
            if (canvas) {
                if (this.coordinates.height<531 || this.coordinates.width<413){
                    this.modal=true
                    // console.log("true")
                }else {
                    this.modal=false
                    this.final = canvas.toDataURL();

                    this.img = true
                    var img = document.createElement('img');
                    let reader = new FileReader();

                    img.onload = () =>{
                        var canva = document.getElementById('canvas');
                        var ctx = canva.getContext('2d');
                        canva.width = 413;
                        canva.height = 531;
                        ctx.drawImage(img, 0, 0, 413, 531);
                        var dataURI = canva.toDataURL();
                        // this.fin = dataURI
                    // console.log('tagdasdasd', this.fin=dataURI)
                        //console.log("pre", this.final)
                        // console.log("pos", this.fin)

                        // this.$emit('imagen64', dataURI)

                        axios.post("/intranet/administracion/update-image",{
                            fotografia:dataURI,
                            id:idEstudiante
                        })
                        .then(response => {
                            console.log(response.data);
                            if (response.data.status)
                            {
                                toastr.success(response.data.message);
                                this.getEstudiante();
                            }
                            else
                            {
                                toastr.error(response.data.message);
                            }
                        })

                    }
                    img.src= this.final
                }
            }
            else{
                this.modal = true;
                // console.log("true2")
            }
        },
        change({ coordinates, canvas }) {
            this.coordinates = coordinates;

            this.image = canvas.toDataURL();

        },
        getEstudiante(){
            let dni = this.dni != '' ? this.dni : 0
            axios.get("/intranet/administracion/estudiante/"+dni).then(response => {
                console.log(Object.keys(response.data).length);
                if (Object.keys(response.data).length != 0) {
                    this.foto = response.data.foto;
                    this.errorDni = ''
                    this.nombres = response.data.nombres;
                    this.paterno = response.data.paterno;
                    this.materno = response.data.materno;
                    this.id = response.data.id;
                }else
                {
                    this.foto = '';
                    this.errorDni = 'No se encontraron resultados, intentelo de nuevo.'
                    this.nombres = '';
                    this.paterno = '';
                    this.materno = '';
                    this.id = '';
                }
            });
        }
    },
    computed:{
        nombresCompletos(){
            return `${this.paterno} ${this.materno} ${this.nombres}`
        }
    }
}
</script>
<style>

</style>
