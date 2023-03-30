import { Component, Input } from '@angular/core';
import { NgForm } from '@angular/forms';
import { CustomerInfo } from '../_models/CustomerInfo';
import { BookingResponse } from '../_models/Flights/BookingResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-book-flight',
  templateUrl: './book-flight.component.html',
  styleUrls: ['./book-flight.component.css']
})
export class BookFlightComponent {
  @Input() flightInfo!: FlightInfoResponse;
  formSubmitted: boolean = false;
  model: CustomerInfo = { firstName: "", lastName: "", email: "", dateOfBirth: Date(), passengerType: "Adult" }
  bookingResponse: BookingResponse = { bookingReference: "", success: false };
  constructor(private _flightService: FlightService) { }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.flightInfo.data.flightOffers[0].passengers = [];
      this.flightInfo.data.flightOffers[0].passengers.push(this.model);
      this._flightService.getFlightConfirmation(this.flightInfo.data.flightOffers[0]).subscribe(response => {
        this.bookingResponse = response;
        this.formSubmitted = true;
        return true;
      })
      return true;
    }
  }
}
