<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import PostEntry from './Components/PostEntry.vue';
import Sidebar from './Layouts/Sidebar.vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps(['profile']);
const posts = reactive({
    info: {},
    data: [],
});
const loaded = ref(false);
const intersect = ref(null);


const page = usePage();

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

onMounted(function () {
    loaded.value = false;
    axios.get('/posts/user/' + props.profile.user.username).then(
        function (res) {
            loaded.value = true;
            posts.info = res.data.paginated;
            posts.data = res.data.paginated.data;
            console.log(posts.data);
        }
    );
});

watch(intersect, value => {
    if (value != null) {
        observer.observe(value);
    }
});
</script>

<script>
export default {
    layout: Sidebar,
}
</script>

<template>
    <div class="flex flex-row gap-x-6 w-[40rem] max-w-[40rem] flex-shrink-0 border">
        <div class="w-52 h-52 bg-gray-400 flex-shrink-0"></div>
        <div class="flex flex-col justify-center gap-y-4 flex-grow">
            <div class="w-full">
                <p class="text-xl leading-5 font-medium">{{ profile.user.name }}</p>
                <p class="text-sm">@{{ profile.user.username }}</p>
            </div>

            <p v-if="profile.user.bio">
                {{ profile.user.bio }}
            </p>
            <p v-else>
                No bio..
            </p>
            <div class="flex flex-row items-center gap-x-6">
                <div class="flex flex-col items-center">
                    <p>Friends</p>
                    <p>{{ profile.friendsCount }}</p>
                </div>
                <div class="w-[1px] h-full bg-gray-800"></div>
                <div class="flex flex-col items-center">
                    <p>Posts</p>
                    <p>{{ profile.postsCount }}</p>
                </div>
                <div class="w-[1px] h-full bg-gray-800"></div>
                <div class="flex flex-col items-center">
                    <p>Likes</p>
                    <p>{{ profile.likesCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TODO: Add posts here -->
    <PostEntry v-for="post in posts.data" :post="post" v-if="loaded" />

    <div ref="intersect" v-if="loaded"></div>
</template>