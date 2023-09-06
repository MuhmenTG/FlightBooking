import { Component, Input } from '@angular/core';
import { NgForm } from '@angular/forms';
import { FlightBookingResponse } from '../_models/Flights/FlightBookingResponse';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { PaymentInfo } from '../_models/PaymentInfo';
import { FlightService } from '../_services/flight.service';
import { Router } from '@angular/router';
import { FinalBookingResponse } from '../_models/Flights/FinalBookingResponse';

@Component({
  selector: 'app-payment',
  templateUrl: './payment.component.html',
  styleUrls: ['./payment.component.css']
})
export class PaymentComponent {
  @Input() flightInfo!: FlightInfoResponse;
  @Input() bookingResponse!: FlightBookingResponse;
  isLoading: boolean = false;
  model: PaymentInfo = { bookingReference: "", cardNumber: "", expireMonth: "", expireYear: "", cvcDigits: "", grandTotal: "" };

  constructor(private _flightService: FlightService, private _router: Router) { }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.isLoading = true;
      this.model.bookingReference = this.bookingResponse.bookingReference;
      this.model.grandTotal = this.flightInfo.data.flightOffers[0].price.grandTotal;
      this._flightService.getPaymentConfirmation(this.model).subscribe({
        next: response => {
          this._router.navigateByUrl('/bookingconfirmation', { state: response })
        },
        error: err => {
          this.isLoading = false;
        }
      })
    }
  }

  ngAfterViewChecked() {
    if (this.isLoading) {
      let mySpinner = document.getElementById('spinner');
      if (mySpinner != null) mySpinner.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
