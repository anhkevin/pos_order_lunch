<template>
    <div class="avatar d-flex" :class="sizeCurrent" style="border-radius: 100%; padding: 0px;" :style="{background: bg_current}">
        <img :src="avatar_current" width="100%" class="m-auto">
    </div>
</template>

<script>
export default {

    props: {
        size: {
            type: String,
            default: 'md'
        },
        userid: {
            type: String,
        },
        isauth: {
            type: Boolean,
            default: false,
        }

    },
    data() {
        return {
            avatar: ['alligator_lg.png','anteater_lg.png','axolotl_lg.png','badger_lg.png','bat_lg.png','beaver_lg.png','buffalo_lg.png','camel_lg.png','capybara_lg.png','chameleon_lg.png','cheetah_lg.png','chinchilla_lg.png','chipmunk_lg.png','chupacabra_lg.png','cormorant_lg.png','coyote_lg.png','crow_lg.png','dingo_lg.png','dinosaur_lg.png','dolphin_lg.png','duck_lg.png','elephant_lg.png','ferret_lg.png','frog_lg.png','giraffe_lg.png','grizzly_lg.png','hedgehog_lg.png','hippo_lg.png','ibex_lg.png','ifrit_lg.png','jackal_lg.png','jackalope_lg.png','kangaroo_lg.png','koala_lg.png','kraken_lg.png','lemur_lg.png','leopard_lg.png','liger_lg.png','llama_lg.png','mink_lg.png','monkey_lg.png','narwhal_lg.png','orangutan_lg.png','panda_lg.png','penguin_lg.png','pumpkin_lg.png','python_lg.png','quagga_lg.png','rabbit_lg.png','raccoon_lg.png','rhino_lg.png','sheep_lg.png','shrew_lg.png','skunk_lg.png','squirrel_lg.png','tiger_lg.png','turtle_lg.png','walrus_lg.png','wolverine_lg.png','wombat_lg.png'],
            bg: ['rgb(181, 74, 74)', 'rgb(137 149 11)', 'rgb(26 109 14)', 'rgb(32 111 84)', 'rgb(30 130 173)', 'rgb(140 80 161)', 'rgb(203 23 143)', 'rgb(197 75 99)', 'rgb(33 150 243)', 'rgb(96 125 139)'],
            avatar_current: '',
            bg_current: '',
            sizeCurrent: '',
        }
    },

    created() {
        this.auto_avatar();
        this.setSize();        
    },

    methods: {
        auto_avatar() {
            let array_number_user_id = this.userid.split("");

            // index for background
            let key_bg = array_number_user_id[array_number_user_id.length-1];
            this.bg_current = this.bg[key_bg];

            // index for avatar
            if (this.avatar[this.userid] !== undefined) {
                this.avatar_current = '/images/avatar/' + this.avatar[this.userid];
            } else {
                let total_number = 0;
                array_number_user_id.forEach((number, index) => {       
                    total_number = Number(total_number) + Number(number)
                });

                if (this.avatar[total_number] !== undefined) {
                    this.avatar_current = '/images/avatar/' + this.avatar[total_number];
                } else {
                    let array_number_avatar = total_number.toString().split("");
                    let key_avatar = array_number_avatar[array_number_avatar.length-1];
                    this.avatar_current = '/images/avatar/' + this.avatar[key_avatar];
                }
            }
            
        },

        setSize() {
            switch(this.size) {
                case 'xs':
                    this.sizeCurrent = 'width30 height30';
                    break;
                case 'sm':
                    this.sizeCurrent = 'width40 height40';
                    break;
                case 'md':
                    this.sizeCurrent = 'width50 height50';
                    break;
                case 'lg':
                    this.sizeCurrent = 'width60 height60';
                    break;
                default:
                    this.sizeCurrent = 'width40 height40';
                    break;
            }
        }
    }
}
</script>