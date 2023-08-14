import { Component, OnInit } from '@angular/core';
import { AgentService } from '../_services/agent.service';

@Component({
  selector: 'app-agent',
  templateUrl: './agent.component.html',
  styleUrls: ['./agent.component.css']
})
export class AgentComponent implements OnInit {
  agentToken = sessionStorage.getItem('token');

  constructor(private _agentService: AgentService) { }

  ngOnInit(): void {
    this._agentService.setHttpOptionsAccount(this.agentToken!);
    console.log(this.agentToken)

    this._agentService.getAllFlightBookings().subscribe(response => {
      console.log(response);
    })
  }
}
