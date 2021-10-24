<template>
    <div v-click-outside="closeDropdown">
        <input 
            ref="searchString" 
            v-model="state.searchString" 
            @click="toggleDropdown()" 
            @keyup="keyUp($event)" 
            type="text" 
            class="searchInput"
            placeholder="search"
            />
        <div v-show="state.showDropdown" class="dropdown">
            <div v-for="item in visibleCoins" @click="addCoin(item)" class="item flex" :class="item.selected ? 'item-selected' : null">
                <div>
                    <img :src="item.iconUrl" class="h-5 w-5 rounded-full">
                </div>
                <div class="ml-4">{{ item.symbol }}</div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .searchInput {
        font-family: 'Material Icons';
        font-size: 18px;
        width: 84px;
        border: 1px solid #ddd;
        border-radius: 20px;
        transition: width 0.3s ease-out;
    }

    .searchInput:focus {
        font-family: inherit;
        width: 160px;
        transition: width 0.3s ease-out;
    }

    .dropdown {
        background: rgb(252, 252, 252);
        border: 1px solid #eee;
        width: 200px;
        position: absolute;
        z-index: 9999;
    }

    .item {
        padding: 10px;
        position: relative;
        border: 1px solid #f5f5f5;
    }

    .item-selected {
        background: #eee;
        font-weight: bold;
        padding-left: 12px;
    }

    .item:hover {
        background: #ddd;
        cursor: pointer;
    }

    @keyframes example {
        from {background-color: red;}
        to {background-color: yellow;}
    }

</style>

<script>
    import { computed, defineComponent, reactive, watch } from 'vue'

    const VISIBLE_COUNT = 8;
    const KEY_MOVE_DOWN = 'ArrowDown'
    const KEY_MOVE_UP = 'ArrowUp'
    const KEY_FILTER = 'Enter'

    export default defineComponent({
        props: {
            list: Array,
        },
        setup(props, context) {
            watch(props, () => {
                state.filteredList = getListFiltered()
            });

            const state = reactive({
                showDropdown: false,    
                filteredList: props.list,
                selectionPosition: -1,
                searchString: ''
            });
            
            const visibleCoins = computed(() => {
                return state.filteredList.slice(0, VISIBLE_COUNT)
            })

            function closeDropdown() {
                state.showDropdown = false;
            }

            function toggleDropdown() {
                state.showDropdown = !state.showDropdown;
            }

            function keyUp(event) {
                const direction = (event.key === KEY_MOVE_DOWN) ? 1 : (event.key === KEY_MOVE_UP) ? -1 : null;

                if (direction) {
                    moveSelection(direction);
                    return
                }

                if(event.key === KEY_FILTER) {
                    addCoin(state.filteredList[state.selectionPosition])
                    return;
                }

                filter(this);
            }

            function moveSelection(direction) {
                if(! canMove(direction)) return;

                state.showDropdown = true;
                state.selectionPosition += direction;

                if(state.filteredList[state.selectionPosition]) {
                    state.filteredList[state.selectionPosition].selected = true;
                }
                
                if(state.selectionPosition > 0 || (state.selectionPosition >= 0 && direction == -1)) {
                    state.filteredList[state.selectionPosition - (1 * direction) ].selected = false;
                }
            }

            function addCoin(item) {
                context.emit('addCoin', item)
                state.showDropdown = false;
                resetPosition()
            }

            function filter(current) {
                state.filteredList = getListFiltered(false)
                state.showDropdown = true;
                resetPosition()
            }

            function getListFiltered(select) {
                return props.list.filter((item) => {
                    if(select) item.selected = select;
                    return item.symbol.toLowerCase().includes(state.searchString.toLowerCase())
                })
            }

            function canMove(direction) {
                return (direction == -1 && state.selectionPosition > 0) 
                || (direction == 1 && state.selectionPosition < state.filteredList.length - 1)
            }

            function resetPosition() {
                state.selectionPosition = -1;
            }

            return { state, toggleDropdown, keyUp, visibleCoins, closeDropdown, addCoin }
        },
    })
</script>
