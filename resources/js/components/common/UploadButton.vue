<template>
    <v-button color="accent" style-type="air"
              class="m-btn--custom m-btn--icon m--margin-right-10 btn btn-accent upload-file"
              :loading="loading">
        <span>
            <i class="la la-cloud-upload"></i>
            <span>Tải lên</span>
        </span>
        <input type="file" @change="uploadFile">
    </v-button>
</template>

<script>
  import axios from 'axios'

  export default {
    props: {
      url: {
        type: String,
      },
      postData:{
        type: Object,
        default: {}
      }
    },
    data() {
      return {
        loading: false
      }
    },
    methods: {
      async uploadFile(e) {
        this.loading = true;

        let file = e.target.files[0];
        let frmData = new FormData();
        frmData.append('file', file);

        for (let row of Object.entries(this.postData)) {
          frmData.append(row[0], row[1]);
        }

        try{
          let response = await axios.post(this.url, frmData)
          this.$emit("response", response)

          this.loading = false

        }catch(e){

          this.loading = false
        }
      }
    }
  }
</script>

<style>
    .upload-file {
        position: relative;
    }

    .upload-file input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
    }
</style>