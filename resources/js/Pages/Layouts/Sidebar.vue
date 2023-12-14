<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeMount, onMounted, ref, watch } from 'vue';

const props = defineProps(['auth']);
const requestsCount = ref(0);

const message = useForm({
    message: '',
    visibility: 'friends',
});

function postMessage() {
    message.post("/post/create", {
        onSuccess: () => {
            message.reset();
        }
    });
}

onBeforeMount(function () {
    axios.get('/me/count/friendrequest').then(
        function (res) {
            requestsCount.value = res.data.friendRequestCount;
        }
    );
});

onMounted(function () {
    setInterval(function () {
        axios.get('/me/count/friendrequest').then(
            function (res) {
                requestsCount.value = res.data.friendRequestCount;
                console.log(res);
            }
        );
    }, 30000);
});
</script>

<template>
    <div class="flex flex-row justify-between overflow-visible">
        <div class="h-screen flex flex-col gap-y-4 sticky top-0 py-20 ml-48 min-w-[22rem]">
            <div class="border h-20"></div>
            <div class="border h-48 flex flex-col mt-12 justify-between">
                <div class="flex flex-col items-center gap-y-4 relative -top-1/4">
                    <div class="h-24 w-24 bg-gray-300">

                    </div>

                    <div class="flex flex-col leading-none items-center">
                        <p class="text-lg font-medium">{{ $page.props.auth.user.name }}</p>
                        <p>@{{ $page.props.auth.user.username }}</p>
                    </div>
                </div>

                <div class="flex flex-row gap-x-2 justify-center w-full mb-8">
                    <Link as="button" type="button" href="">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.754 14a2.249 2.249 0 0 1 2.25 2.249v.575c0 .894-.32 1.76-.902 2.438-1.57 1.834-3.957 2.739-7.102 2.739-3.146 0-5.532-.905-7.098-2.74a3.75 3.75 0 0 1-.898-2.435v-.577a2.249 2.249 0 0 1 2.249-2.25h11.501Zm0 1.5H6.253a.749.749 0 0 0-.75.749v.577c0 .536.192 1.054.54 1.461 1.253 1.468 3.219 2.214 5.957 2.214s4.706-.746 5.962-2.214a2.25 2.25 0 0 0 .541-1.463v-.575a.749.749 0 0 0-.749-.75ZM12 2.004a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"
                            fill="#212121" />
                    </svg>
                    </Link>
                    <Link as="button" type="button" href="">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.012 2.25c.734.008 1.465.093 2.182.253a.75.75 0 0 1 .582.649l.17 1.527a1.384 1.384 0 0 0 1.927 1.116l1.401-.615a.75.75 0 0 1 .85.174 9.792 9.792 0 0 1 2.204 3.792.75.75 0 0 1-.271.825l-1.242.916a1.381 1.381 0 0 0 0 2.226l1.243.915a.75.75 0 0 1 .272.826 9.797 9.797 0 0 1-2.204 3.792.75.75 0 0 1-.848.175l-1.407-.617a1.38 1.38 0 0 0-1.926 1.114l-.169 1.526a.75.75 0 0 1-.572.647 9.518 9.518 0 0 1-4.406 0 .75.75 0 0 1-.572-.647l-.168-1.524a1.382 1.382 0 0 0-1.926-1.11l-1.406.616a.75.75 0 0 1-.849-.175 9.798 9.798 0 0 1-2.204-3.796.75.75 0 0 1 .272-.826l1.243-.916a1.38 1.38 0 0 0 0-2.226l-1.243-.914a.75.75 0 0 1-.271-.826 9.793 9.793 0 0 1 2.204-3.792.75.75 0 0 1 .85-.174l1.4.615a1.387 1.387 0 0 0 1.93-1.118l.17-1.526a.75.75 0 0 1 .583-.65c.717-.159 1.45-.243 2.201-.252Zm0 1.5a9.135 9.135 0 0 0-1.354.117l-.109.977A2.886 2.886 0 0 1 6.525 7.17l-.898-.394a8.293 8.293 0 0 0-1.348 2.317l.798.587a2.881 2.881 0 0 1 0 4.643l-.799.588c.32.842.776 1.626 1.348 2.322l.905-.397a2.882 2.882 0 0 1 4.017 2.318l.11.984c.889.15 1.798.15 2.687 0l.11-.984a2.881 2.881 0 0 1 4.018-2.322l.905.396a8.296 8.296 0 0 0 1.347-2.318l-.798-.588a2.881 2.881 0 0 1 0-4.643l.796-.587a8.293 8.293 0 0 0-1.348-2.317l-.896.393a2.884 2.884 0 0 1-4.023-2.324l-.11-.976a8.988 8.988 0 0 0-1.333-.117ZM12 8.25a3.75 3.75 0 1 1 0 7.5 3.75 3.75 0 0 1 0-7.5Zm0 1.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z"
                            fill="#212121" />
                    </svg>
                    </Link>
                    <Link as="button" type="button" href="/logout" method="post">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.502 11.5a1.002 1.002 0 1 1 0 2.004 1.002 1.002 0 0 1 0-2.004Z" fill="#212121" />
                        <path
                            d="M12 4.354v6.651l7.442-.001L17.72 9.28a.75.75 0 0 1-.073-.976l.073-.084a.75.75 0 0 1 .976-.073l.084.073 2.997 2.997a.75.75 0 0 1 .073.976l-.073.084-2.996 3.004a.75.75 0 0 1-1.134-.975l.072-.085 1.713-1.717-7.431.001L12 19.25a.75.75 0 0 1-.88.739l-8.5-1.502A.75.75 0 0 1 2 17.75V5.75a.75.75 0 0 1 .628-.74l8.5-1.396a.75.75 0 0 1 .872.74Zm-1.5.883-7 1.15V17.12l7 1.236V5.237Z"
                            fill="#212121" />
                        <path
                            d="M13 18.501h.765l.102-.006a.75.75 0 0 0 .648-.745l-.007-4.25H13v5.001ZM13.002 10 13 8.725V5h.745a.75.75 0 0 1 .743.647l.007.102.007 4.251h-1.5Z"
                            fill="#212121" />
                    </svg>
                    </Link>
                </div>
            </div>

            <div class="">
                <button type="button" class="border py-6 px-6 flex flex-row justify-between w-full">
                    Friend Requests ({{ requestsCount }})
                    <p>></p>
                </button>

                <div class="flex flex-col max-h-56 h-56 border border-t-0">

                </div>
            </div>
        </div>

        <div class="flex flex-col gap-y-6 py-20 flex-shrink-0">
            <slot></slot>
        </div>

        <div class="h-screen flex flex-col gap-y-4 sticky top-0 py-20 mr-48 min-w-[22rem]">
            <div class="h-12 p-2 flex flex-row justify-evenly">
                <Link as="button" type="button" href="/home" method="get">friends</Link>
                <Link as="button" type="button" href="/home/public" method="get">public</Link>
                <Link as="button" type="button" href="/home/notifications" method="get">notifs.</Link>
            </div>
            <form @submit.prevent="postMessage" class="border h-72 p-4 flex flex-col justify-between">
                <h1 class="font-medium text-lg">write a post!</h1>
                <textarea name="post" id="post" cols="1" rows="5" v-model="message.message" class="resize-none"></textarea>
                <p>visibility: <span>
                        <input type="radio" name="vis-pub" value="public" v-model="message.visibility"> <label
                            for="vis-pub">public </label>
                        <input type="radio" name="vis-pub" value="friends" v-model="message.visibility"> <label
                            for="vis-pub">friends </label>
                        <input type="radio" name="vis-me" value="only me" v-model="message.visibility"> <label
                            for="vis-me">only me </label>
                    </span></p>
                <button type="submit" class="w-fit">cend!</button>
            </form>
        </div>
    </div>
</template>