import { Component, OnInit } from '@angular/core';
import { AdminService } from '../_services/admin.service';
import { NgForm } from '@angular/forms';
import { AccountRequest } from '../_models/Employees/AccountRequest';
import { AccountsResponse, agent as Agent } from '../_models/Employees/AccountsResponse';
import { Router } from '@angular/router';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent implements OnInit {

  status = ["Active", "Inactive"];
  model: AccountRequest = { email: "", firstName: "", lastName: "", status: "1", isAdmin: 0, isAgent: 1, agentId: 0 };
  accounts: AccountsResponse = { formatedAgents: [] };
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
        console.log(response);
      });
    }
    else {
      this._router.navigateByUrl('/')
    }
  }

  editAccount(account: Agent) {
    this.model.agentId = account.agentId;
    this.model.firstName = account.firstName;
    this.model.lastName = account.lastName;
    this.model.isAdmin = account.administratorPermission;
    this.model.isAgent = account.travelAgentPermission;
    this.model.email = account.email;
    this.model.status = account.accountStatus;
  }

  accountActivation(account: Agent) {
    this._adminService.deactivateOrActivateAccount(account.agentId).subscribe(() => {
      let agent = this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.agentId == account.agentId)]

      if (agent.accountStatus == '0') agent.accountStatus = '1';
      else agent.accountStatus = '0';

      this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.agentId == agent.agentId)] = agent;
    });
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    }
    else if (this.model.agentId != 0) {
      this._adminService.editAgent(this.model).subscribe({
        next: response => {
          this._snackBar.open('User successfully edited!', '', this.snackbarOptions)
          this.accounts.formatedAgents[this.accounts.formatedAgents.findIndex(i => i.agentId === response.data.agentId)] = response.data;
        }, error: err => {
          this._snackBar.open(err, '', this.snackbarOptions)
        }
      }
      )
    }
    else {
      console.log(this.model);
      this._adminService.createAccount(this.model).subscribe(() => {
        this._snackBar.open('User successfully created!', '', this.snackbarOptions)
      });
    }

    this.model.agentId = 0;
    this.model.firstName = '';
    this.model.lastName = '';
    this.model.isAdmin = 0;
    this.model.isAgent = 1;
    this.model.email = '';
    this.model.status = '1';
  }
}
