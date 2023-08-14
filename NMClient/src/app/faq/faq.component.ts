import { Component, OnInit } from '@angular/core';
import { PublicService } from '../_services/public.service';
import { FAQS } from '../_models/FAQS';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html',
  styleUrls: ['./faq.component.css']
})

export class FaqComponent implements OnInit {
  faqs: FAQS = { FAQS: [] };

  constructor(private _publicService: PublicService) { }

  ngOnInit(): void {
    this._publicService.getAllFaqs().subscribe(response => {
      this.faqs = response;

      console.log(this.faqs);
    })
  }
}
