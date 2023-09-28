import { Component, OnInit } from '@angular/core';
import { AdminService } from '../_services/admin.service';
import { Router } from '@angular/router';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { FAQRequest, FAQS } from '../_models/FAQS';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'app-admin-faq',
  templateUrl: './admin-faq.component.html',
  styleUrls: ['./admin-faq.component.css']
})
export class AdminFaqComponent implements OnInit {
  model: FAQRequest = { id: 0, question: '', answer: '' }
  role: boolean = false;
  faqs: FAQS = { FAQS: [] };
  snackbarOptions: MatSnackBarConfig = { verticalPosition: "top", horizontalPosition: "center" }

  constructor(private _adminService: AdminService, private _router: Router, private _snackBar: MatSnackBar) { };

  ngOnInit(): void {
    if (sessionStorage.getItem('role') == 'admin') {
      this.role = true;
      var token = sessionStorage.getItem('token');
      if (token != null) {
        this._adminService.setHttpOptionsAccount(token);
      }

      this._adminService.getAllFAQ().subscribe(response => {
        this.faqs = response;
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
      this._adminService.editFAQ(this.model).subscribe({
        next: response => {
          this._snackBar.open('FAQ successfully edited!', '', this.snackbarOptions)
        }, error: err => {
          this._snackBar.open(err.message, '', this.snackbarOptions)
        }
      }
      )
    }
    else {
      this._adminService.createFAQ(this.model).subscribe(() => {
        this._snackBar.open('FAQ successfully created!', '', this.snackbarOptions)
      });
    }

    this.model.id = 0;
    this.model.question = '';
    this.model.answer = '';
  }

  editFAQ(faq: FAQRequest) {
    this.model.id = faq.id;
    this.model.question = faq.question;
    this.model.answer = faq.answer;
  }

  deleteFAQ(faqId: number) {
    this._adminService.removeFAQ(faqId).subscribe(() => {
      this._snackBar.open('FAQ deleted successfully!', '', this.snackbarOptions)
    });
  }
}
