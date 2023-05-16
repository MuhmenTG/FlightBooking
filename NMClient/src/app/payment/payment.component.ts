import { Component, Input } from '@angular/core';
import { NgForm } from '@angular/forms';
import { FlightBookingResponse } from '../_models/Flights/FlightBookingResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { PaymentInfo } from '../_models/PaymentInfo';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-payment',
  templateUrl: './payment.component.html',
  styleUrls: ['./payment.component.css']
})
export class PaymentComponent {
  @Input() flightInfo!: FlightInfoResponse;
  @Input() bookingResponse!: FlightBookingResponse;
  model: PaymentInfo = { bookingReference: "", cardNumber: "", expireMonth: "", expireYear: "", cvcDigits: "", grandTotal: "" }

  constructor(private _flightService: FlightService) { }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.model.bookingReference = this.bookingResponse.bookingReference;
      this.model.grandTotal = this.flightInfo.data.flightOffers[0].price.grandTotal;
      this._flightService.getPaymentConfirmation(this.model).subscribe(x => {
        console.log(JSON.stringify(x));
      })
      return true;
    }
  }
}
