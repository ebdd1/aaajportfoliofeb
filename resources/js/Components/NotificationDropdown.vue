<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { BellIcon } from '@heroicons/vue/24/outline';

const notifications = ref([]);
const unreadCount = ref(0);
const isOpen = ref(false);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('api.notifications.index'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error('Failed to fetch notifications', error);
    }
};

const markAsRead = async (id) => {
    try {
        await axios.post(route('api.notifications.read', id));
        fetchNotifications();
    } catch (error) {
        console.error('Failed to mark notification as read', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('api.notifications.read-all'));
        fetchNotifications();
    } catch (error) {
        console.error('Failed to mark all as read', error);
    }
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && notifications.value.length === 0) {
        fetchNotifications();
    }
};

const closeDropdown = (e) => {
    if (!e.target.closest('.notification-dropdown')) {
        isOpen.value = false;
    }
};

onMounted(() => {
    fetchNotifications();
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

const getNotificationColor = (status) => {
    switch(status) {
        case 'paid': return 'text-green-600 bg-green-50';
        case 'cancelled': return 'text-red-600 bg-red-50';
        case 'expired': return 'text-gray-600 bg-gray-50';
        default: return 'text-blue-600 bg-blue-50';
    }
};
</script>

<template>
    <div class="relative notification-dropdown">
        <button @click="toggleDropdown" class="relative p-2 text-ink hover:text-terracotta focus:outline-none transition-colors duration-200">
            <BellIcon class="w-6 h-6" />
            <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-terracotta rounded-full">
                {{ unreadCount }}
            </span>
        </button>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="isOpen" class="absolute right-0 z-50 w-80 mt-2 origin-top-right bg-paper border border-oat-dark rounded-md shadow-lg outline-none overflow-hidden">
                <div class="px-4 py-3 border-b border-oat-dark flex justify-between items-center">
                    <p class="text-sm font-semibold text-ink">Notifications</p>
                    <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-terracotta hover:text-terracotta-dark">
                        Mark all as read
                    </button>
                </div>
                
                <div class="max-h-64 overflow-y-auto">
                    <template v-if="notifications.length > 0">
                        <div v-for="notification in notifications" :key="notification.id" 
                            class="px-4 py-3 border-b border-oat-dark hover:bg-oat cursor-pointer transition-colors duration-150"
                            :class="{'bg-oat/30': notification.read_at === null}"
                            @click="markAsRead(notification.id)">
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 pt-1">
                                    <span class="inline-block w-2 h-2 rounded-full" :class="notification.read_at ? 'bg-transparent' : 'bg-terracotta'"></span>
                                </div>
                                <div class="ml-3 w-0 flex-1">
                                    <p class="text-sm font-medium text-ink" :class="{'font-bold': !notification.read_at}">
                                        {{ notification.data.message || 'Notification' }}
                                    </p>
                                    <div class="mt-1 flex items-center justify-between text-xs text-taupe">
                                        <span v-if="notification.data.status" class="px-2 py-0.5 rounded-full font-medium" :class="getNotificationColor(notification.data.status)">
                                            {{ notification.data.status }}
                                        </span>
                                        <span>{{ new Date(notification.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div v-else class="px-4 py-6 text-center text-sm text-taupe">
                        No notifications yet.
                    </div>
                </div>
                
                <div class="px-4 py-2 border-t border-oat-dark bg-oat/50 text-center">
                    <Link :href="route('ushome.user')" class="text-xs font-medium text-terracotta hover:text-terracotta-dark">
                        View all orders
                    </Link>
                </div>
            </div>
        </transition>
    </div>
</template>
