import { Component, OnInit } from '@angular/core';
import { AgentService } from '../_services/agent.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-agent',
  templateUrl: './agent.component.html',
  styleUrls: ['./agent.component.css']
})
export class AgentComponent implements OnInit {
  agentToken = sessionStorage.getItem('token');
  role: boolean = false;

  constructor(private _agentService: AgentService, private _router: Router) { }

  ngOnInit(): void {
    if (sessionStorage.getItem('role') == 'agent'){
      this._agentService.setHttpOptionsAccount(this.agentToken!);
      console.log(this.agentToken)
  
      this._agentService.getAllFlightBookings().subscribe(response => {
        console.log(response);
      })
    }
    else{
      this._router.navigateByUrl('/');
    }
  }
}
