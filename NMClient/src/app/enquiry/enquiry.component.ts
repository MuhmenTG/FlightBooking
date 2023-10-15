import { Component } from '@angular/core';
import { PublicService } from '../_services/public.service';
import { NgForm } from '@angular/forms';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { EnquiryRequest } from '../_models/Enquiries/EnquiryRequest';

@Component({
  selector: 'app-enquiry',
  templateUrl: './enquiry.component.html',
  styleUrls: ['./enquiry.component.css']
})
export class EnquiryComponent {
  model: EnquiryRequest = { name: '', subject: '', message: '', email: '', bookingReference: '' }
  snackbarOptions: MatSnackBarConfig = { verticalPosition: "top", horizontalPosition: "center" }

  constructor(private _publicService: PublicService, private _snackBar: MatSnackBar) { }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this._publicService.sendEnquiry(this.model).subscribe({
        next: response => {
          console.log(response);
          this._snackBar.open('Enquiry sent. You will be contacted via the provided email.', '', this.snackbarOptions);

          this.model.name = '';
          this.model.subject = '';
          this.model.message = '';
          this.model.email = '';
          this.model.bookingReference = '';
        }
      })
    }
  }
}
