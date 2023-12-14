<script setup>
import Sidebar from './Layouts/Sidebar.vue';
import PostEntry from './Components/PostEntry.vue';


</script>

<script>
export default {
    layout: Sidebar,
}
</script>

<template>
    <PostEntry :post="post" v-for="post in $page.props.paginated.data" />
</template>