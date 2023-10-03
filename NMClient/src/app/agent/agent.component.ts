import { Component, OnInit } from '@angular/core';
import { AgentService } from '../_services/agent.service';
import { Router } from '@angular/router';
import { NgForm } from '@angular/forms';
import { PassengerInfo } from '../_models/PassengerInfo';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { BookingResponse, Passenger } from '../_models/Employees/Agent/Booking';

@Component({
  selector: 'app-agent',
  templateUrl: './agent.component.html',
  styleUrls: ['./agent.component.css']
})
export class AgentComponent implements OnInit {
  agentToken = sessionStorage.getItem('token');
  role: boolean = false;
  model: Passenger = { id: 0, firstName: '', lastName: '', dateOfBirth: '', bookingReference: '', email: '' }
  bookingResponses: BookingResponse = { bookings: [] };
  snackbarOptions: MatSnackBarConfig = { verticalPosition: "top", horizontalPosition: "center" }
  currentYear = new Date().getFullYear();
  currentMonth = new Date().getMonth();
  currentDate = new Date().getDate();
  maxDate = new Date(this.currentYear, this.currentMonth, this.currentDate);

  constructor(private _agentService: AgentService, private _router: Router, private _snackBar: MatSnackBar) { }

  ngOnInit(): void {
    if (sessionStorage.getItem('role') == 'agent') {
      this.role = true;
      var token = sessionStorage.getItem('token');
      if (token != null) {
        this._agentService.setHttpOptionsAccount(token);
      }

      this._agentService.getAllFlightBookings().subscribe(response => {
        this.bookingResponses = response;
        console.log(this.bookingResponses);
      });
    }
    else {
      this._router.navigateByUrl('/')
    }
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    }
    else if (this.model.id != 0) {
      this._agentService.editPassengerInformation(this.model).subscribe({
        next: response => {
          this._snackBar.open('Passenger information successfully edited!', '', this.snackbarOptions)
          // this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.id === response.data.id)] = response.data;
        }, error: err => {
          this._snackBar.open(err.message, '', this.snackbarOptions)
        }
      }
      )
    }
    else {
      this._snackBar.open('Choose a passenger to edit.', '', this.snackbarOptions)
    }

    this.model.id = 0;
    this.model.firstName = '';
    this.model.lastName = '';
    this.model.email = '';
    this.model.dateOfBirth = '';
    this.model.bookingReference = '';
  }

  editBooking(passenger: Passenger) {
    this.model.id = passenger.id;
    this.model.firstName = passenger.firstName;
    this.model.lastName = passenger.lastName;
    this.model.email = passenger.email;
    this.model.dateOfBirth = passenger.dateOfBirth;
    this.model.bookingReference = passenger.bookingReference;
    console.log(this.model.bookingReference);
  }
}
