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
            avatar: ['alligator_lg.png', 'leopard_lg.png', 'lemur_lg.png', 'dragon_lg.png', 'ferret_lg.png', 'dolphin_lg.png', 'otter_lg.png', 'chipmunk_lg.png', 'dinosaur_lg.png', 'raccoon_lg.png'],
            bg: ['rgb(181, 74, 74)', 'rgb(137 149 11)', 'rgb(26 109 14)', 'rgb(32 111 84)', 'rgb(30 130 173)', 'rgb(140 80 161)', 'rgb(203 23 143)', 'rgb(197 75 99)'],
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
            let position_auto_bg = Math.floor(Math.random() * (this.bg.length - 1));
            let position_auto_avatar = Math.floor(Math.random() * (this.avatar.length - 1));

            let local_pos = localStorage.getItem('pos_local');

            let local_pos_id = '';
            let local_pos_bg = '';
            let local_pos_avatar = '';

            if(JSON.parse(local_pos)) {
                local_pos_id = JSON.parse(local_pos)['id'];
                local_pos_bg = JSON.parse(local_pos)['pos_bg'];
                local_pos_avatar = JSON.parse(local_pos)['pos_avatar'];
            }

            if( (local_pos_id != this.userid) && this.isauth )
            {
                localStorage.removeItem('pos_local');
            }
            
            if(!local_pos && this.isauth) {
                localStorage.setItem('pos_local', JSON.stringify({id: this.userid, pos_bg: position_auto_bg, pos_avatar: position_auto_avatar}));
            }

            if(this.userid == local_pos_id) {
                this.bg_current = this.bg[local_pos_bg];
                this.avatar_current = '/images/avatar/' + this.avatar[local_pos_avatar];
            }
            else {
                this.bg_current = this.bg[position_auto_bg];
                this.avatar_current = '/images/avatar/' + this.avatar[position_auto_avatar];
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