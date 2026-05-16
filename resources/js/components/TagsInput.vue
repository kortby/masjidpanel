<script setup lang="ts">
import { ref } from 'vue';
import { X } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    modelValue: string[];
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string[]];
}>();

const inputValue = ref('');

const addTag = () => {
    const value = inputValue.value.trim();
    if (value && !props.modelValue.includes(value)) {
        emit('update:modelValue', [...props.modelValue, value]);
    }
    inputValue.value = '';
};

const removeTag = (index: number) => {
    const newTags = [...props.modelValue];
    newTags.splice(index, 1);
    emit('update:modelValue', newTags);
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag();
    } else if (e.key === 'Backspace' && inputValue.value === '' && props.modelValue.length > 0) {
        removeTag(props.modelValue.length - 1);
    }
};
</script>

<template>
    <div class="flex flex-col gap-2">
        <div 
            v-if="modelValue.length > 0" 
            class="flex flex-wrap gap-2 mb-2"
        >
            <span 
                v-for="(tag, index) in modelValue" 
                :key="index"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-sm font-medium bg-emerald-100 text-emerald-800"
            >
                {{ tag }}
                <button 
                    type="button" 
                    @click="removeTag(index)"
                    class="rounded-full hover:bg-emerald-200 p-0.5 text-emerald-600 hover:text-emerald-900 focus:outline-none"
                >
                    <X class="h-3 w-3" />
                </button>
            </span>
        </div>
        
        <Input
            v-model="inputValue"
            type="text"
            @keydown="handleKeydown"
            @blur="addTag"
            :placeholder="placeholder || 'Type and press Enter to add tags'"
            class="w-full"
        />
        <p class="text-xs text-stone-500">Press Enter or comma to add a tag.</p>
    </div>
</template>
