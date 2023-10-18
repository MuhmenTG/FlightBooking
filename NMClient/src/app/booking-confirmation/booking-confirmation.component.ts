import { Component, OnInit } from '@angular/core';
import { FinalBookingResponse } from '../_models/Flights/FinalBookingResponse';
import { Router } from '@angular/router';
import { PublicService } from '../_services/public.service';

@Component({
  selector: 'app-booking-confirmation',
  templateUrl: './booking-confirmation.component.html',
  styleUrls: ['./booking-confirmation.component.css']
})
export class BookingConfirmationComponent implements OnInit {
  booking: FinalBookingResponse = { bookingReference: "", navigationId: 0, passengers: [], flights: [] }
  isValid: boolean = false;

  constructor(private _router: Router, private _publicService: PublicService) { }

  ngOnInit(): void {
    this.booking = history.state;

    if (!this.booking.passengers) {
      this._router.navigateByUrl('');
    }

    this.isValid = true;
  }

  resendEmail(bookingReference: string, email: string) {
    this._publicService.resendEmail(bookingReference, email).subscribe(response => {
      console.log(response);
    });
  }
}
