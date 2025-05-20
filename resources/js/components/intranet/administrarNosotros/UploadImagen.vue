<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-xs-12 contain">
        <div class="form-row">
          <label class="col-md-6 col-xs-12 control-label"
            ><b>Fotografía Digital (*.jpg)</b></label
          >

          <div class="col-md-6 col-xs-12">
            <div class="input-group mb-3">
              <div class="custom-file">
                <!-- <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01"> -->
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="openModal()"
                >
                  <i class="fa fa-image"></i> Selecionar
                </button>
              </div>
            </div>
          </div>
          <div class="col-md-3 offset-md-4 col-xs-12 form-group" id="foto">
            <div id="show" v-if="img">
              <canvas class="img-fluid img-thumbnail" id="canvas"></canvas>
            </div>
            <input type="hidden" id="fotografia" name="foto" />
            <small class="text-danger error" id="error_foto"></small>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="uploadModal"
      data-backdrop="static"
      data-keyboard="false"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              Seleccione y recorte
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-8 col-xs-12">
                <div class="form-group">
                  <div class="input-group">
                    <div class="custom-file">
                      <input
                        type="file"
                        id="image_data"
                        name="image"
                        @change="GetImage"
                        class="custom-file-input"
                      />
                      <label class="custom-file-label" for="exampleInputFile"
                        >1. Selecionar Imagen</label
                      >
                    </div>
                  </div>
                  <div class="alert alert-info" role="alert">
                    <small
                      ><li>
                        El recorte de la imagen debe ser superior a 413 px de
                        ancho y 531 px de alto
                      </li></small
                    >
                  </div>
                  <div
                    v-if="modal"
                    class="alert alert-danger mt-0"
                    role="alert"
                  >
                    <small
                      ><li>El recorte de la imagen es muy pequeña</li></small
                    >
                  </div>
                  <span class="badge badge-danger" id="error_imagen"></span>
                </div>
                <div class="container">
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
                          adjustStencil: true,
                        }"
                        image-restriction="fit-area"
                        default-boundaries="fill"
                        :stencil-props="{
                          aspectRatio: 10 / 12,
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
                        <img
                          :src="image"
                          class="img-fluid"
                          alt="Responsive image"
                        />
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label for=""
                        >Ancho:{{ coordinates.width }}
                        <b id="ancho"></b>px</label
                      >
                      <br />
                      <label for=""
                        >Alto:{{ coordinates.height }}
                        <b id="alto"></b>px</label
                      >
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 col-xs-12">
                <p>
                  <b>Realice el recorte de acuerdo al siguiente ejemplo.</b>
                </p>

                <img
                  class="img-thumbnail rounded"
                  src="/images/ejemplo-foto.jpg"
                  width="150"
                />
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              v-on:click="cerrar()"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Cerrar
            </button>
            <button
              type="button"
              v-on:click="guardar()"
              class="btn btn-primary"
            >
              Guardar Cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Cropper } from "vue-advanced-cropper";
import { ResizeEvent } from "vue-advanced-cropper";
import $ from "jquery";
export default {
  components: {
    Cropper,
  },
  data() {
    return {
      image: null,
      img: false,
      modal: false,
      avatar: null,
      final: null,
      fin: null,
      coordinates: {
        width: 0,
        height: 0,
        left: 0,
        top: 0,
      },
      limitations: {
        minWidth: 412,
        minHeight: 430,
        maxWidth: 413 + 50,
        maxHeight: 531 + 50,
      },
    };
  },
  methods: {
    pixelsRestriction({
      minWidth,
      minHeight,
      maxWidth,
      maxHeight,
      imageWidth,
      imageHeight,
    }) {
      return {
        minWidth: minWidth,
        minHeight: minHeight,
        maxWidth: maxWidth,
        maxHeight: maxHeight,
      };
    },
    GetImage(e) {
      let image = e.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(image);
      reader.onload = (e) => {
        this.avatar = e.target.result;
        //this.NuevoRedic(this.avatar, 413, 531)
      };
    },
    toggleModal() {
      this.modal = !this.modal;
    },
    cerrar() {
      this.avatar = null;
      this.image = null;
      this.coordinates.width = null;
      this.coordinates.height = null;
      this.coordinates.top = null;
      this.coordinates.left = null;
      this.modal = false;

      $("#uploadModal").modal("hide");
    },
    openModal() {
      $("#uploadModal").modal("show");
      $("#uploadModal").on("shown.bs.modal", function () {
        if ($(".modal-backdrop").length > 1) {
          $(".modal-backdrop").not(":first").remove();
        }
      });
    },
    guardar() {
      const { canvas } = this.$refs.cropper.getResult();
      if (canvas) {
        if (this.coordinates.height < 531 || this.coordinates.width < 413) {
          this.modal = true;
        } else {
          this.final = canvas.toDataURL();

          $("#uploadModal").modal("hide");
          // $(".modal-backdrop").remove();
          this.img = true;
          var img = document.createElement("img");
          let reader = new FileReader();

          img.onload = () => {
            var canva = document.getElementById("canvas");
            var ctx = canva.getContext("2d");
            canva.width = 413;
            canva.height = 531;
            ctx.drawImage(img, 0, 0, 413, 531);
            var dataURI = canva.toDataURL();
            this.fin = dataURI;
            // console.log('tagdasdasd', this.fin=dataURI)
            //console.log("pre", this.final)
            //console.log("pos", this.fin)
            this.$emit("imagen64", this.fin);
          };
          img.src = this.final;
        }
      } else {
        this.modal = true;
      }
    },
    change({ coordinates, canvas }) {
      this.coordinates = coordinates;

      this.image = canvas.toDataURL();
    },
  },
  computed: {},
};
</script>
<style></style>
