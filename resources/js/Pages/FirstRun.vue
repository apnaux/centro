<script setup>
import { useForm } from '@inertiajs/vue3';

const setup = useForm({
    'name': '',
    'bio': '',
});

function setCredentials() {
    setup.post('/hello/set');
}
</script>

<template>
    <div class="w-screen h-screen flex flex-col justify-center items-center gap-y-4">
        <form @submit.prevent="setCredentials" class="flex flex-col gap-y-6 p-6 border border-black w-96 m-auto">
            <h1 class="font-medium text-lg">welcome to centro! Please set your information.</h1>
            <input type="text" name="name" id="name" v-model="setup.name" placeholder="Your Name">
            <input type="text" name="bio" id="bio" v-model="setup.bio" placeholder="Your Bio">
            <button type="submit">Let's go!</button>
        </form>
    </div>
</template>