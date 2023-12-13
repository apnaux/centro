<script setup>
import Sidebar from './Layouts/Sidebar.vue';
import PostEntry from './Components/PostEntry.vue';

defineOptions({
    layout: Sidebar,
})
</script>

<template>
    <PostEntry :post="post" v-for="post in $page.props.paginated.data" />
</template>