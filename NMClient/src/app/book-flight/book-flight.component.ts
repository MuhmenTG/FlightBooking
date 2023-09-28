import { Component, Input, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { CustomerInfo } from '../_models/CustomerInfo';
import { FlightBookingResponse } from '../_models/Flights/FlightBookingResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightService } from '../_services/flight.service';
import { PassengerCount } from '../_models/Flights/PassengerCount';
import { PassengerInfo } from '../_models/PassengerInfo';

@Component({
  selector: 'app-book-flight',
  templateUrl: './book-flight.component.html',
  styleUrls: ['./book-flight.component.css']
})

export class BookFlightComponent implements OnInit {
  @Input() flightInfo!: FlightInfoResponse;
  @Input() passengerCount: PassengerCount;
  formSubmitted: boolean = false;
  gender: string[] = ["Male", "Female"]
  model: CustomerInfo = { gender: "", firstName: "", lastName: "", email: "", dateOfBirth: Date(), passengerType: "" }
  passengersAdults: CustomerInfo[] = [];
  passengersChildren: CustomerInfo[] = [];
  passengersInfants: CustomerInfo[] = [];
  bookingResponse: FlightBookingResponse = { bookingReference: "", success: false };

  // Clean this up some day - Date class
  currentYear = new Date().getFullYear();
  currentMonth = new Date().getMonth();
  currentDate = new Date().getDate();
  isLoading: boolean = false;
  flightBooked: boolean = false;
  flightBookedHasResponse: boolean = false;
  maxDate = new Date(this.currentYear, this.currentMonth, this.currentDate);
  constructor(private _flightService: FlightService) { }

  ngOnInit(): void {
    for (let index = 0; index < this.passengerCount.adults; index++) {
      this.passengersAdults[index] = { gender: "Male", firstName: "", lastName: "", dateOfBirth: Date(), passengerType: "Adult" }
    }

    for (let index = 0; index < this.passengerCount.children; index++) {
      this.passengersChildren[index] = { gender: "Male", firstName: "", lastName: "", dateOfBirth: Date(), passengerType: "Child" }
    }

    for (let index = 0; index < this.passengerCount.infants; index++) {
      this.passengersInfants[index] = { gender: "Male", firstName: "", lastName: "", dateOfBirth: Date(), passengerType: "Infant" }
    }
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.flightBooked = true;
      this.isLoading = true;
      this.flightInfo.data.flightOffers[0].passengers = this.passengersAdults.concat(this.passengersChildren, this.passengersInfants)

      this.flightInfo.data.flightOffers[0].passengers.forEach(passenger => {
        passenger.email = this.model.email;
      });

      this._flightService.getFlightConfirmation(this.flightInfo.data.flightOffers[0]).subscribe({
        next: response => {
          this.bookingResponse = response;
          this.flightBookedHasResponse = true;
          this.isLoading = false;
          this.formSubmitted = true;
          return true;
        },
        error: err => {
          this.isLoading = false;
          this.flightBooked = false;
        }
      })
      return true;
    }
  }

  ngAfterViewChecked() {
    if (this.isLoading) {
      let mySpinner = document.getElementById('spinner');
      if (mySpinner != null) mySpinner.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
