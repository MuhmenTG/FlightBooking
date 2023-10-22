import { Component, OnInit } from '@angular/core';
import { AdminService } from '../_services/admin.service';
import { NgForm } from '@angular/forms';
import { AccountRequest } from '../_models/Employees/AccountRequest';
import { AccountsResponse, agent as Agent } from '../_models/Employees/AccountsResponse';
import { Router } from '@angular/router';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { AccountResponse } from '../_models/Employees/AccountResponse';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent implements OnInit {
  searchString: string = '';
  status = ["Active", "Inactive"];
  model: AccountRequest = { email: "", firstName: "", lastName: "", status: "1", isAdmin: 0, isAgent: 1, id: 0 };
  accounts: AccountsResponse = {} as AccountsResponse;
  searchAccount: AccountResponse = {} as AccountResponse;
  role: boolean = false;
  snackbarOptions: MatSnackBarConfig = { verticalPosition: "top", horizontalPosition: "center" }

  constructor(private _adminService: AdminService, private _router: Router, private _snackBar: MatSnackBar) { };

  ngOnInit(): void {
    if (sessionStorage.getItem('role') == 'admin') {
      this.role = true;
      var token = sessionStorage.getItem('token');
      if (token != null) {
        this._adminService.setHttpOptionsAccount(token);
      }

      this._adminService.getListOfAccounts().subscribe(response => {
        this.accounts = response
      });
    }
    else {
      this._router.navigateByUrl('/')
    }
  }

  editAccount(account: Agent) {
    this.model.id = account.id;
    this.model.firstName = account.firstName;
    this.model.lastName = account.lastName;
    this.model.isAdmin = account.administratorPermission;
    this.model.isAgent = account.travelAgentPermission;
    this.model.email = account.email;
    this.model.status = account.accountStatus;
  }

  accountActivation(account: Agent) {
    this._adminService.deactivateOrActivateAccount(account.id).subscribe(() => {
      let agent = this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.id == account.id)]

      if (agent.accountStatus == '0') agent.accountStatus = '1';
      else agent.accountStatus = '0';

      this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.id == agent.id)] = agent;
    });
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    }
    else if (this.model.id != 0) {
      this._adminService.editAgent(this.model).subscribe({
        next: response => {
          this._snackBar.open('User successfully edited!', '', this.snackbarOptions)
          this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.id === response.data.id)] = response.data;
        }, error: err => {
          this._snackBar.open(err.message, '', this.snackbarOptions)
        }
      }
      )
    }
    else {
      this._adminService.createAccount(this.model).subscribe(response => {
        this.accounts.formatedAgents = this.accounts.formatedAgents.concat(response.data);
        this._snackBar.open('User successfully created!', '', this.snackbarOptions)
      });
    }

    this.model.id = 0;
    this.model.firstName = '';
    this.model.lastName = '';
    this.model.isAdmin = 0;
    this.model.isAgent = 1;
    this.model.email = '';
    this.model.status = '1';
  }

  searchAgent() {
    if (Number(this.searchString) > 0) {
      this._adminService.getAccountDetails(Number(this.searchString)).subscribe({
        next: response => {
          this.searchAccount = response;
        }
      })
    }
    else {
      this.searchAccount = {} as AccountResponse;
    }
  }

  // TODO: Reset password for account
}
