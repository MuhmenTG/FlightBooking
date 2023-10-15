import { Component, OnInit } from '@angular/core';
import { AgentService } from '../_services/agent.service';
import { Router } from '@angular/router';
import { MatSnackBar, MatSnackBarConfig } from '@angular/material/snack-bar';
import { EnquiryResponses } from '../_models/Enquiries/EnquiryResponse';
import { EnquiryReply } from '../_models/Employees/Agent/EnquiryReply';

@Component({
  selector: 'app-agent-enquiry',
  templateUrl: './agent-enquiry.component.html',
  styleUrls: ['./agent-enquiry.component.css']
})
export class AgentEnquiryComponent implements OnInit {
  agentToken = sessionStorage.getItem('token');
  role: boolean = false;
  reply: EnquiryReply = { id: 0, responseMessageToUser: '' }
  responses: EnquiryResponses = { enquiryResponses: [] };
  snackbarOptions: MatSnackBarConfig = { verticalPosition: "top", horizontalPosition: "center" }

  ngOnInit(): void {
    if (sessionStorage.getItem('role') == 'agent') {
      this.role = true;
      var token = sessionStorage.getItem('token');
      if (token != null) {
        this._agentService.setHttpOptionsAccount(token);
      }

      this._agentService.getAllEnquiries().subscribe(response => {
        this.responses = response;
        console.log(this.responses);
      });
    }
    else {
      this._router.navigateByUrl('/')
    }
  }

  constructor(private _agentService: AgentService, private _router: Router, private _snackBar: MatSnackBar) { }

  submitReply(id: number) {
    this.reply.id = id;

    this._agentService.answerUserEnquiry(this.reply).subscribe(response => {
      console.log(response);
      this._snackBar.open('Enquiry has been replied to.', '', this.snackbarOptions);
    })
  }

  close(id: number) {
    this._agentService.setEnquiryStatus(id).subscribe(response => {
      console.log(response);
    })
  }
}
