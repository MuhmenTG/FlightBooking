import { Component, OnInit } from '@angular/core';
import { PublicService } from '../_services/public.service';
import { MyBookingRequest } from '../_models/Flights/myBookingRequest';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-my-bookings',
  templateUrl: './my-booking.component.html',
  styleUrls: ['./my-booking.component.css']
})
export class MyBookingComponent implements OnInit {

  model: MyBookingRequest = { bookingReference: "" }

  constructor(private _publicService: PublicService) { }

  ngOnInit(): void {
    // this._publicService.retrieveBooking("DIVE4D").subscribe(response => {
    //   console.log(response);
    // })
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this._publicService.retrieveBooking(this.model.bookingReference).subscribe(response => {
        console.log(response);
      })
    }
  }
}
