import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
  title = 'NM Flights';
  accessToken = {};

  constructor(private _flightService: FlightService) {}

  ngOnInit(): void{
    this._flightService.getAccessToken().subscribe(x => {this.accessToken = x; console.log(x)});
  }
}
