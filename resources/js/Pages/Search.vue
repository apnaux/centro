<script setup>
import Sidebar from './Layouts/Sidebar.vue';
import PostEntry from './Components/PostEntry.vue';
import axios from 'axios';
import { onMounted, reactive, ref, watch } from 'vue';

const props = defineProps(['paginated']);

const intersect = ref(null);
const loaded = ref(true);
const query = ref('');
const results = reactive({
    info: {},
    data: [],
});

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            if (results.info.next_page_url != null) {
                load_posts_scroll()
            }
        }
    });
});

function load_posts_scroll() {
    axios.get(results.info.next_page_url).then(
        function (res) {
            results.info = res.data.search;
            results.data = res.data.search.data;
        }
    );
}

function load_post() {
    loaded.value = false;
    axios.get('/search/' + query.value).then(
        function (res) {
            loaded.value = true;
            results.info = res.data.search;
            results.data = res.data.search.data;
        }
    );
}

let timeout;

watch(query, value => {
    clearTimeout(timeout);

    if (value.length == 0) {
        results.info = {};
        results.data = [];
        loaded.value == true;
        return 0;
    }

    timeout = setTimeout(() => {
        load_post();
    }, 500)
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
    <input type="text" name="search" id="search" placeholder="Search for a user.."
        class="w-[40rem] px-2 h-12 placeholder:px-2 border-2 border-black" autocomplete="off" v-model="query">
    <div v-if="query.length == 0">Search for users here...</div>
    <div v-if="loaded == false">Loading search results...</div>

    <div v-for="result in results.data">
        <div class="border flex flex-row justify-between items-center">
            <div class="flex flex-row items-center gap-x-4">
                <div class="h-20 w-20 bg-gray-300">

                </div>

                <div class="flex flex-col">
                    <p class="font-medium leading-none">{{ result.name }}</p>
                    <p class="text-sm">@{{ result.username }}</p>
                </div>
            </div>

            <div class="flex flex-row gap-x-2 items-center mr-4">
                <p>
                    <span v-if="result.friend.is_friend == false && result.friend.friend_request.pending">Request Pending</span>
                    <span v-else-if="result.friend.is_friend == false && result.friend.friend_request.confirm">
                        <Link as="button" type="button" :href="'/friend/request/accept/' + result.username" method="post"
                            preserve-scroll preserve-state>Confirm Request</Link>
                    </span>
                    <span v-else-if="result.friend.is_friend == false">
                        <Link as="button" type="button" :href="'/friend/request/add/' + result.username" method="post"
                            preserve-scroll preserve-state>+
                        Add as Friend!</Link>
                    </span>
                </p>
                <Link as="button" type="button" :href="'/profile/' + result.username">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.47 4.22a.75.75 0 0 0 0 1.06L15.19 12l-6.72 6.72a.75.75 0 1 0 1.06 1.06l7.25-7.25a.75.75 0 0 0 0-1.06L9.53 4.22a.75.75 0 0 0-1.06 0Z"
                        fill="#212121" />
                </svg>
                </Link>
            </div>
        </div>
    </div>
    <div v-if="loaded && results.data.length == 0 && query.length != 0">There are no search results 😢</div>
    <div ref="intersect" v-if="loaded && query.length != 0"></div>
</template>