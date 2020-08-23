<template>
    <div class="m-scrollable"
         :style="{
            height: scrollHeight + 'px'
         }">
        <slot></slot>
    </div>
</template>

<script>
  import PerfectScrollbar from 'perfect-scrollbar'

  export default {
    name: 'PerfectScrollbar',
    props: {
      height: {
        type: Number,
        default: 200
      },
      outerHeight: {
        type: Number,
        default: null
      }
    },
    data: () => {
      return {
        scrollbar: null,
        scrollHeight: this.height,
      }
    },

    mounted() {
      this.initScrollbar()
      this.autoResize()
    },
    methods: {
      initScrollbar() {
        this.scrollbar = new PerfectScrollbar(this.$el, {
          wheelSpeed: 0.5,
          swipeEasing: true,
          wheelPropagation: false,
          minScrollbarLength: 40,
          suppressScrollX: true
        })
      },
      autoResize() {
        $(document).ready(() => {
          if (this.outerHeight) {
            this.resize()

            $(window).resize(() => this.resize());
          }
        });
      },
      resize() {
        this.scrollHeight = $(window).height() - this.outerHeight -15
      },
      onTop(callback) {
        $(this.$el).on('ps-y-reach-start', callback)
      },
      onBottom(callback) {
        $(this.$el).on('ps-y-reach-end', callback)
      },
      scrollTop(y) {
        this.$el.scrollTop = y
      }
    }
  }
</script>
