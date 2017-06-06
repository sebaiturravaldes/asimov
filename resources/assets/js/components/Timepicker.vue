<template>
	<div class="time" :class="{ 'time-disabled': !hour.available }" @click="selectedTime">
		{{ hour.time }} {{ hour.period }} 
 		<span v-show="hour.available" class="label label-success">Available</span>
		<span v-show="!hour.available" class="label label-danger">Occupied</span>
	</div>
</template>

<script>
	export default {
		name: 'timePicker',
		props: ['hour'],
		methods: {
			selectedTime() {
				if (this.hour.available) {
					return this.$emit('selectedTime', this.hour);
				}
			}
		}
	}
</script>

<style type="text/css">
.time { 
	border: 1px solid #ccc;
	border-radius: 0px;
	padding: 5px 7.5px;
	max-width: 170px;
	cursor: pointer;
}

.time:hover {
	background: #f5f5f5;
}

.time-disabled, .time-disabled:hover {
	background: #f5f5f5;
    cursor: default;
}
</style>