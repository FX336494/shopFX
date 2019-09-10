<template>
    <div class="swiper-container">
        <div class="swiper-wrapper">
          <slot></slot>
        </div>
        <div class="swiper-pagination swiper-pagination-white"></div>
    </div>
</template>

<script>
    export default {
        // props: ['listImg'],
        mounted() {
            // console.log(this.listImg)
            this.swiper1();

        },
        updated() {
            this.swiper1();
        },
        methods: {
            swiper1() {
                var swiper = new Swiper('.swiper-container', {
                    pagination: {el:'.swiper-pagination'},
                    paginationClickable: true,
                    loop: true,
                    speed: 600,
                    autoplay: true,
                    direction:'horizontal',
                    onTouchEnd: function() {
                        swiper.startAutoplay()
                    }
                });
            }
        }
    }
</script>

<style>
    .swiper-container {
        width: 100%;
        /*height: 18rem;*/
        /*height: 100%;*/
    }
    .swiper-wrapper {
        width: 100%;
        height: 100%;
    }
    .swiper-slide {
        background-position: center;
        background-size: cover;
        width: 100%;
        height: 100%;
    }
    .swiper-slide img {
        width: 100%;
        /*height: 100%;*/
    }
    .swiper-pagination-bullet {
        width:0.833rem;
        height: 0.833rem;
        display: inline-block;
        background: #7c5e53;
    }
</style>
