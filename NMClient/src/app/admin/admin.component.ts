import { Component, OnInit } from '@angular/core';
import { AdminService } from '../_services/admin.service';
import { NgForm } from '@angular/forms';
import { AccountRequest } from '../_models/Employees/AccountRequest';
import { AccountResponse, agent as Agent } from '../_models/Employees/AccountResponse';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export class AdminComponent implements OnInit {

  model: AccountRequest = { email: "", firstName: "", lastName: "", status: "", isAdmin: 0, isAgent: 1, agentId: 0 };
  accounts: AccountResponse = { formatedAgents: [] };

  constructor(private _adminService: AdminService) { };

  ngOnInit(): void {
    var token = sessionStorage.getItem('token');
    if (token != null) {
      this._adminService.setHttpOptionsAccount(token);
    }

    this._adminService.getListOfAccounts().subscribe(response => {
      this.accounts = response
      console.log(response);
    });
  }

  editAccount(account: Agent) {
    console.log("Account: " + account.travelAgentPermission)
    this.model.agentId = account.agentId;
    this.model.firstName = account.firstName;
    this.model.lastName = account.lastName;
    this.model.isAdmin = account.administratorPermission;
    this.model.isAgent = account.travelAgentPermission;
    this.model.email = account.email;
    this.model.status = account.accountStatus;
  }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    }
    else if (this.model.agentId != 0) {
      this._adminService.editAgent(this.model).subscribe(response => {
        console.log(response);
      })
    }
    else {

      console.log(this.model);
      this._adminService.createAccount(this.model).subscribe(response => {
        console.log(response);
      });
    }

    this.model.agentId = 0;
  }
}
