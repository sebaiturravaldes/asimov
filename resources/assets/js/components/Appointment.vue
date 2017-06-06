<template>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Select day and time to have an appointment with death.</div>

				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12 col-md-6">
							
							<!-- Datepicker Component -->
							<datepicker v-model="state.date" 
							@selected="selectedDay" 
							@input="changeState" 
							:disabled="state.disabled" 
							:inline="true" 
							language="es"></datepicker>
						</div>
						<div class="col-xs-offset-2 col-xs-8 col-md-offset-1 col-md-4">
							
							<!-- Spinner loading -->
							<spinner v-show="loading"></spinner>

							<!-- Transition with timepicker component -->
							<transition-group name="component-fade" 
							mode="out-in" 
							v-show="showTimePicker">
							<timepicker v-for="(hour, key) in schedule" 
							:hour="hour" 
							:key="key" 
							v-on:selectedTime="selectedTime"></timepicker>
						</transition-group>		
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</template>

<script>
/**
 * Import Datepicker Component to select current data
 * source: https://www.npmjs.com/package/vuejs-datepicker
 */
 import Datepicker from 'vuejs-datepicker'

/**
 * Require  sweet alert
 * sourte: http://t4t5.github.io/sweetalert/
 */
 var SweetAlert = require('sweetalert')

 /**
  * Require moment library, for get current date.
  * source: https://momentjs.com/
  */
  var moment = require('moment')

  export default {
    components:{
     Datepicker,
   },
   data() {
     return {
  			/**
  			 * loading, is used from Spinner component
  			 * @type {Boolean}
  			 */
  			 loading: false,

  			/**
  			 * timePicker, is used for instance schedule
  			 * @type {Object}
  			 */
  			 timePicker: {
  			 	start_time: {
  			 		time: 9,
  			 		period: 'am'
  			 	},
  			 	end_time: {
  			 		time: 18,
  			 		period: 'pm'
  			 	}
  			 },

 			/**
 			 * schedule, is used to timepicker component
 			 * @type {Array}
 			 */
 			 schedule: [],

 			/**
 			 * showTimePicker, is used for show time with selected date
 			 * @type {Boolean}
 			 */
 			 showTimePicker: false,

 			/**
 			 * state, is used for Datepicker component
 			 * @type {Object}
 			 */
       state: {
         disabled: {
          to: new Date(moment().format('YYYY-MM-DD')),
          days: [6,0],
        }
      }
    }
  },

  methods: {
 		/**
 		 * selectedDay()
 		 * is activate with selected event in Datepicker component
 		 * 
 		 */
 		 selectedDay() {
 		 	this.showTimePicker = true
 		 },

      	/**
      	 * changeState()
      	 * is activated with 'state' value is change
      	 */
      	 changeState(){
      	 	let date            = moment(this.state.date).format('YYYY-MM-DD')

      	 	const self          = this
      	 	this.showTimePicker = false
      	 	this.loading        = true

      	 	axios.get('appointment/' + date)
      	 	.then(function (response) {
      	 		self.refreshSchelude(response.data)
      	 		self.loading        = false
      	 		self.showTimePicker = true
      	 	})
      	 	.catch(function (error) {
      	 		console.log(error)
      	 	})

      	 },

 		/**
 		 * refreshSchelude()
 		 * refresh 'schedule' value
 		 * 
 		 * @param  {Object} data contain response with fetch http
 		 */
 		 refreshSchelude(data) {
 		 	const self = this
 		 	this.schedule = this.schedule.filter(function item(item) {
 		 		item.available = true
 		 		return item
 		 	})

 		 	$.each(data, function(key, value) {
 		 		$.each(self.schedule, function(key_schedule, value_schedule) {
 		 			if (value.time_appointment == value_schedule.time) {
 		 				self.schedule[key_schedule].available = false
 		 				return false
 		 			}
 		 		})
 		 	})
 		 },

 		 /**
 		  * selectedTime()
 		  * Select a time for the appointment
 		  * 
 		  * @param  {Object} hour contain time, period in state (available or not)
 		  */
      selectedTime(hour) {
       const self = this

       SweetAlert({
        title: "Appointment",
        text: "Please enter your email:",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "my@example.com"
      },
      function(inputValue){
        if (inputValue === false) return false

 				// Email regex used to validate email formats.
 				// sourte: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/email
 				// var re = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/

 				if (inputValue === "") {
 					swal.showInputError("You need to write something!")
 					return false
 				}
        // else if(!re.test(inputValue)){
 				// 	swal.showInputError("invalid email!")
 				// 	return false
 				// }
 				
 				axios.post('appointment', {
 					date_appointment: moment(self.state.date).format('YYYY-MM-DD'),
 					time_appointment: hour.time,
 					email: inputValue,
 				})
 				.then(function (response) {
 					hour.available = false
 					swal("Nice!", "You have scheduled an appointment to Dance with the Death", "success")
 				})
 				.catch(function (errors) {
          if(errors.response.status == 422){

            for(var error in errors.response.data){
             errors.response.data[error].forEach(e => {
              swal.showInputError(e)
            })
           }

         }else{
          console.log(errors)
        }

        return false
      })
 			})
     },

 		 /**
 		  * createSchelude()
 		  * This method allows to load with data the array of objects of the agenda in his office hours.
 		  */
      createSchelude() {
        let obj        = {}
        let period     = ''
        let time       = ''
        let start_time = this.timePicker.start_time.time
        let end_time   = this.timePicker.end_time.time

        if (this.timePicker.end_time.time < 12 && this.timePicker.end_time.period.toLowerCase() == 'pm') {
          end_time += 12
        }

        for (var i = start_time; i <= end_time; i++) {
          period = (i < 12) ? 'am' : 'pm'
          time   = (i < 10) ? '0' + i : i
          time   += ':00:00'

          obj = {
           time: time, 
           period: period,
           available: true
         }

         this.schedule.push(obj)
       }

     }
   },

   mounted() {
    this.createSchelude()
  }

}
</script>

<style type="text/css">
  .component-fade-enter-active, .component-fade-leave-active {
   transition: opacity .3s ease;
 }
 .component-fade-enter, .component-fade-leave-to {
   opacity: 0;
 }
</style>