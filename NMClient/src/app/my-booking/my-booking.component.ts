import { Component, OnInit } from '@angular/core';
import { PublicService } from '../_services/public.service';
import { MyBookingRequest } from '../_models/Flights/myBookingRequest';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-my-bookings',
  templateUrl: './my-booking.component.html',
  styleUrls: ['./my-booking.component.css']
})
export class MyBookingComponent implements OnInit {

  isLoading: boolean = false;
  model: MyBookingRequest = { bookingReference: "" }

  constructor(private _publicService: PublicService, private _router: Router) { }

  ngOnInit(): void {
    // this._publicService.retrieveBooking("DIVE4D").subscribe(response => {
    //   console.log(response);
    // })
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.isLoading = true;
      this._publicService.retrieveBooking(this.model.bookingReference).subscribe(response => {
        this._router.navigateByUrl('/bookingconfirmation', { state: response })
        this.isLoading = false;
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
