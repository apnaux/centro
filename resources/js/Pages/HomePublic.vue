<script setup>
import Sidebar from './Layouts/Sidebar.vue';
import PostEntry from './Components/PostEntry.vue';
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';

const props = defineProps(['paginated']);

const intersect = ref(null);
const loaded = ref(false);
const posts = reactive({
    info: {},
    data: [],
})

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            if(posts.info.next_page_url != null){
                load_posts();
            }
        }
    });
});

function load_posts() {
    axios.get(posts.info.next_page_url).then(
        function (res) {
            posts.info = res.data.paginated;
            res.data.paginated.data.forEach(value => {
                posts.data.push(value);
            });
            console.log(posts.data);
        }
    );
}

onMounted(() => {
    loaded.value = false;
    axios.get('/home/public/retrieve').then(
        function (res) {
            loaded.value = true;
            posts.info = res.data.paginated;
            posts.data = res.data.paginated.data;
            console.log(posts.data);
        }
    );
})

watch(intersect, value => {
    if (value != null) {
        observer.observe(value);
    }
});

function deleteOnArray(index){
    console.log(index);
    posts.data.splice(index, 1);
}

</script>

<script>
export default {
    layout: Sidebar,
}
</script>

<template>
    <div v-if="loaded == false">Loading posts...</div>
    <PostEntry :post="post" :index="index" @deleted="deleteOnArray" v-for="(post, index) in posts.data" v-if="loaded"/>

    <div v-if="loaded && posts.data.length == 0">There are no posts 😢</div>
    <div ref="intersect" v-if="loaded"></div>
</template>