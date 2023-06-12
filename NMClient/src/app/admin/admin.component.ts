import { Component } from '@angular/core';
import { AdminService } from '../_services/admin.service';
import { NgForm } from '@angular/forms';
import { AccountRequest } from '../_models/Employees/AccountRequest';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent {

  model: AccountRequest = {email: "", firstName: "", lastName: "", status: "", isAdmin: 0, isAgent: 1};

  constructor(private _adminService: AdminService){};

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      
      console.log(this.model);
      // this._adminService.createAccount(this.model).subscribe(response => {
      //   console.log(response);
      // })
    }
  }
}
