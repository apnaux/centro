<script setup>
import { useForm } from '@inertiajs/vue3';

const post = useForm({
    message: '',
    visibility: '',
});

function postMessage(){
    post.post("/create/post", {});
}
</script>

<template>
    <div class="h-screen w-screen flex flex-row justify-between gap-x-36 overflow-visible">
        <div class="w-[36rem] h-full flex flex-col justify-between sticky py-20 ml-48">
            <div class="border-4 h-24"></div>
            <form @submit.prevent="postMessage" class="border-4 h-72 p-4 flex flex-col justify-between">
                <h1 class="font-medium text-lg">write a post!</h1>
                <textarea name="post" id="post" cols="1" rows="5" v-model="post.message"></textarea>
                <p>visibility: <span>
                    <input type="radio" name="vis-pub" value="public" v-model="post.visibility"> <label for="vis-pub">public </label>
                    <input type="radio" name="vis-me" value="only me" v-model="post.visibility"> <label for="vis-me">only me </label>
                </span></p>
                <button type="submit" class="w-fit">cend!</button>
            </form>
            <div class="border-4 h-80">
                <p>{{ $page.props.auth.user.name }}</p>
                <p>{{ $page.props.auth.user.username }}</p>
            </div>
        </div>

        <div class="flex flex-col w-full overflow-y-scroll overflow-visible gap-y-6 pr-48 py-20">
            <div class="border-4 p-96" v-for="n in 50">
                {{ n }}
            </div>
        </div>
    </div>
</template>