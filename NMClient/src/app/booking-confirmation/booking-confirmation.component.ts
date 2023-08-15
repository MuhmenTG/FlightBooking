import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FinalBookingResponse } from '../_models/Flights/FinalBookingResponse';
import { Router } from '@angular/router';

@Component({
  selector: 'app-booking-confirmation',
  templateUrl: './booking-confirmation.component.html',
  styleUrls: ['./booking-confirmation.component.css']
})
export class BookingConfirmationComponent implements OnInit {
  booking: FinalBookingResponse = { navigationId: 0, passenger: [], flight: []}
  isValid: boolean = false;

  constructor(private _router: Router) {}

  ngOnInit(): void {
    this.booking = history.state;

    if (!this.booking.passenger){
      this._router.navigateByUrl('');
    }

    this.isValid = true;
  }
}
