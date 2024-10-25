<template>
  <div>
    <Popover v-model:open="isOpen">
      <PopoverTrigger as-child @click="togglePopover">
        <Button
          variant="outline"
          :class="['w-full justify-between px-3 hover:bg-white', classProperty]"
          :disabled="disabled"
        >
          <div class="flex items-center truncate">
            <template v-if="$slots['label-content']">
              <slot name="label-content" 
                :selected-item="selectedItem" 
                :selected-items="selectedItems" 
                :multiple="multiple">
                <span :class="['truncate block font-normal', selectedItem ? '' : 'text-gray-800 font-normal']">
                  {{ multiple 
                    ? `${selectedItems.length} selected` 
                    : selectedItem ? displayLabel(selectedItem) : selectPlaceholder 
                  }}
                </span>
              </slot>
            </template>
            <template v-else>
              <span :class="['truncate block font-normal', selectedItem ? '' : 'text-gray-800 font-normal']">
                {{ multiple 
                  ? `${selectedItems.length} selected` 
                  : selectedItem ? displayLabel(selectedItem) : selectPlaceholder 
                }}
              </span>
            </template>
            <!-- <span :class="['truncate block', selectedItem ? '' : 'text-gray-500 font-normal']">
              {{ multiple 
                ? `${selectedItems.length} selected` 
                : selectedItem ? displayLabel(selectedItem) : selectPlaceholder 
              }}
            </span> -->
            <CrossCircledIcon
              v-if="canClear && modelValue"
              class="ml-2 h-4 w-4 text-red-500 shrink-0 hover:text-red-600 font-semibold"
              @click="clearSelection"
            />
          </div>
          <CaretSortIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>

      <PopoverContent class="flex p-0 min-w-[var(--radix-popover-trigger-width)]" :inert="!isOpen">
        <Command>
          <CommandInput
            type="text"
            class="h-9"
            :placeholder="searchPlaceholder"
            v-model="searchQuery"
            @input="handleInput"
          />
          <CommandEmpty class="py-4">
            {{ loading ? 'Searching...' : 'No results found.' }}
          </CommandEmpty>

          <CommandList>
            <CommandGroup>
              <CommandItem v-if="multiple" @select="selectAll">
                <span class="font-bold">Select All</span>
              </CommandItem>

              <CommandItem
                v-for="item in filteredItems"
                :key="itemKey(item)"
                :value="itemValue(item)"
                @select="selectItem(item)"
              >
              <slot name="label" :item="item">
                {{ displayLabel(item) }}
              </slot>
              <CheckIcon :class="checkIconClass(item)" />
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>

    <p v-if="error" class="mt-0.5 text-xs text-red-500 font-semibold">
      This field is required.
    </p>
  </div>
</template>

<script>
import { CaretSortIcon, CheckIcon, CrossCircledIcon } from '@radix-icons/vue'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/Components/ui/command'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'

export default {
  emits: ['search', 'select', 'update:modelValue'],
  components: { 
    CaretSortIcon, CheckIcon, CrossCircledIcon, Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, Popover, PopoverContent, PopoverTrigger 
  },
  props: {
    items: Array,
    modelValue: [String, Number, Object],
    classProperty: { type: String, default: '' },
    labelProperty: { type: [String, Number], default: 'name' },
    valueProperty: { type: [String, Number], default: 'id' },
    placeholder: { type: String, default: 'Select Country' },
    selectPlaceholder: { type: String, default: 'Select Option' },
    searchPlaceholder: { type: String, default: 'Search option...' },
    loading: { type: Boolean, default: false },
    error: [String, Boolean],
    multiple: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    canClear: { type: [Boolean, String], default: false }
  },
  data() {
    return {
      isOpen: false,
      searchQuery: '',
      selectedItems: this.multiple ? (Array.isArray(this.modelValue) ? this.modelValue : []) : this.modelValue || null,
    };
  },
  watch: {
    modelValue(newValue) {
      this.selectedItems = this.multiple ? (Array.isArray(newValue) ? newValue : []) : newValue;
    },
  },
  computed: {
    isObjectItems() {
      return this.items.length > 0 && typeof this.items[0] === 'object';
    },
    selectedItem() {
      if (this.multiple) {
        return this.items.filter(item =>
          this.selectedItems.includes(this.itemValue(item))
        );
      }
      return this.items.find(item => this.itemValue(item) == this.modelValue);
    },
    filteredItems() {
      const query = this.searchQuery.trim().toLowerCase();
      return this.items.filter(item => {
        const label = this.itemLabel(item);
        return label.toString().toLowerCase().includes(query);
      });
    },
    allSelected() {
      return this.filteredItems.length > 0 && this.filteredItems.every(item =>
        this.selectedItems.includes(this.itemValue(item))
      );
    },
  },
  methods: {
    togglePopover() {
      this.isOpen = !this.isOpen;
      this.searchQuery = ''
    },
    itemValue(item) {
      return this.isObjectItems ? item[this.valueProperty] : item;
    },
    itemLabel(item) {
      return this.isObjectItems ? item[this.labelProperty] : item;
    },
    itemKey(item) {
      return this.itemValue(item);
    },
    selectItem(item) {
      const value = this.itemValue(item);
      if (this.multiple) {
        const index = this.selectedItems.indexOf(value);
        if (index === -1) {
          this.selectedItems.push(value);
        } else {
          this.selectedItems.splice(index, 1);
        }
        this.$emit('update:modelValue', this.selectedItems);
      } else {
        this.$emit('update:modelValue', value);
        this.isOpen = false;
      }
      this.$emit('select', item);
    },
    selectAll() {
      this.selectedItems = this.allSelected ? [] : this.filteredItems.map(this.itemValue);
      this.$emit('update:modelValue', this.selectedItems);
    },
    handleInput(event) {
      this.searchQuery = event.target.value;
      this.$emit('search', this.searchQuery);
    },
    clearSelection() {
      this.$emit('update:modelValue', '');
      this.$emit('select', '');
    },
    displayLabel(item) {
      return this.isObjectItems ? item[this.labelProperty] : item;
    },
    checkIconClass(item) {
      const selected = this.multiple
        ? this.selectedItems.includes(this.itemValue(item))
        : this.itemValue(item) === this.modelValue;

      return `ml-auto h-4 w-4 ${selected ? 'opacity-100' : 'opacity-0'}`;
    }
  }
};
</script>

<style>
.popover-content-width-same-as-its-trigger {
  width: var(--radix-popover-trigger-width);
  max-height: var(--radix-popover-content-available-height);
}
</style>
